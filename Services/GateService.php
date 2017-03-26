<?php
/**
 * Copyright © 2015 Pavel A. Puchkov
 *
 * This file is part of the kino-khv.ru project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ItQuasar\C4CoreRbsPaymentGateBundle\Services;

use Doctrine\ORM\EntityManager;
use ItQuasar\C4CoreBundle\AbstractService\Gate;
use ItQuasar\C4CoreBundle\Entity\Order;
use ItQuasar\C4CoreBundle\Entity\BankExchangeDocument;
use ItQuasar\C4CoreBundle\Entity\HttpPayRequest;
use ItQuasar\C4CoreBundle\Exception\BadActionException;
use ItQuasar\C4CoreBundle\Exception\NotFoundBankOrderException;
use ItQuasar\C4CoreBundle\Exception\BankResponseAlreadyProcessed;
use ItQuasar\C4CoreBundle\Exception\GateException;
use ItQuasar\C4CoreBundle\Exception\NotFoundBankPayResponseException;
use ItQuasar\C4CoreBundle\Exception\NotFoundBankPayRequestException;
use ItQuasar\C4CoreBundle\Exception\PayInProcessException;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsGetOrderStatusResponse;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsRegisterOrderRequest;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsRegisterOrderResponse;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Translation\Translator;

class GateService extends Gate
{
    use WsService;
    
    /** @var  EntityManager */
    protected $em;
    /** @var  Translator */
    protected $translator;
    /** @var  Router */
    protected $router;
    /** @var  NotificationService */
    protected $notificationService;

    protected $paySessionTimeoutSecs;

    protected $payReturnRoute;
    protected $payFailRoute;

    protected $payReturnRouteDev;
    protected $payFailRouteDev;

    public function __construct(EntityManager $em, Translator $translator, Router $router, NotificationService $notificationService)
    {
        $this->em = $em;
        $this->translator = $translator;
        $this->router = $router;
        $this->notificationService = $notificationService;
    }
    
    public function processHttpPayResponse(Request $httpRequest)
    {
        $orderId = $httpRequest->get('orderId');

        $query = $this->em->createQueryBuilder()
            ->select('r')
            ->from('ItQuasarC4CoreRbsPaymentGateBundle:RbsRegisterOrderResponse', 'r')
            ->andWhere('r.orderId = :orderId')
            ->andWhere('r.errorCode = 0')
            ->getQuery();

        $query->setParameter(':orderId', $orderId);

        /** @var RbsRegisterOrderResponse $registerOrderResponse */
        $registerOrderResponse = $query->getOneOrNullResult();
        if (!$registerOrderResponse)
            throw new NotFoundBankPayRequestException($this->translator);

        $order = $registerOrderResponse->getOrder();
        if (!$order)
            throw new NotFoundBankOrderException($this->translator, $orderId);

        $responses = $this->notificationService->findPayResponses($order);
        if (empty($responses))
            throw new NotFoundBankPayResponseException($this->translator, $orderId);
            
        return $responses[0];
    }

    public function getHttpPayRequest(Order $order) : HttpPayRequest
    {
        $query = $this->em->createQueryBuilder()
            ->select('r')
            ->from('ItQuasarC4CoreRbsPaymentGateBundle:RbsRegisterOrderResponse', 'r')
            ->where('r.order = :order')
            ->getQuery();

        $query->setParameter(':order', $order);

        /** @var RbsRegisterOrderResponse $oldRegisterOrderResponse */
        $oldRegisterOrderResponse = $query->getOneOrNullResult();

        if (!$oldRegisterOrderResponse)
            throw new \Exception('Http pay request not found.');

        return $this->processRegisterOrderResponse($oldRegisterOrderResponse);
    }

    private function processRegisterOrderResponse(RbsRegisterOrderResponse $registerOrderResponse) : HttpPayRequest {
        if ($registerOrderResponse->getErrorCode() > 0)
            throw new GateException($registerOrderResponse->getErrorMessage(), $registerOrderResponse->getErrorCode());

        $httpRequest = new HttpPayRequest();
        $httpRequest->setUrl($registerOrderResponse->getFormUrl());

        return $httpRequest;
    }

    public function createHttpPayRequest(BankExchangeDocument $bankRequest)
    {
        /** @var RbsRegisterOrderRequest $bankRequest */

        try {
            $oldHttpPayRequest = $this->getHttpPayRequest($bankRequest->getOrder());
            if ($oldHttpPayRequest)
                return $oldHttpPayRequest;
        } catch (GateException $exception) {
            throw $exception;
        } catch (\Exception $exception) {}

        $soap = $this->createSoap();

       /** @noinspection PhpUndefinedMethodInspection */
        $soapResult = $soap->registerOrder($bankRequest);

        $registerOrderResponse = new RbsRegisterOrderResponse($soapResult);
        $registerOrderResponse->setOrder($bankRequest->getOrder());
        $this->em->persist($registerOrderResponse);
        $this->em->flush();
        
        return $this->processRegisterOrderResponse($registerOrderResponse);
    }
    
    public function processPayResponse(BankExchangeDocument &$response)
    {
        /** @var RbsGetOrderStatusResponse $response */

        $query = $this->em->createQueryBuilder()
            ->select('r')
            ->from('ItQuasarC4CoreRbsPaymentGateBundle:RbsGetOrderStatusResponse', 'r')
            ->andWhere('r.orderNumber = :orderNumber')
            ->getQuery();

        $query->setParameter(':orderNumber', $response->getOrderNumber());

        $oldResponse = $query->getOneOrNullResult();
        if ($oldResponse) 
            throw new BankResponseAlreadyProcessed($this->translator, $oldResponse);

        $query = $this->em->createQueryBuilder()
            ->select('r')
            ->from('ItQuasarC4CoreRbsPaymentGateBundle:RbsRegisterOrderRequest', 'r')
            ->where('r.merchantOrderNumber = :orderNumber')
            ->getQuery();

        $query->setParameter(':orderNumber', $response->getOrderNumber());

        /** @var RbsRegisterOrderRequest $request */
        $request = $query->getOneOrNullResult();
        if (!$request)
            throw new NotFoundBankOrderException($this->translator, $response->getOrderNumber());

        $order = $request->getOrder();

        if (!$order)
            throw new NotFoundBankOrderException($this->translator, $response->getOrderNumber());

        $response->setOrder($order);

        if ($response->getOrderStatus() == RbsGetOrderStatusResponse::InACS)
            throw new PayInProcessException($this->translator);

        $this->em->persist($response);

        $order->setPaymentResponseDateTime(new \DateTime());

        if ($response->getOrderStatus() != RbsGetOrderStatusResponse::Payed) {
            $order->setBankStatus(Order::Rejected);
            $this->em->flush();

            $e = new BadActionException($this->translator, $response);
            throw $e;
        }

        $order->setBankStatus(Order::Payed);

        $this->em->flush();
    }

    public function createPayReversalRequest(Order $order, BankExchangeDocument $response)
    {
        
    }

    public function getPayRequest(Order $order)
    {
        $query = $this->em->createQueryBuilder()
            ->select('r')
            ->from('ItQuasarC4CoreRbsPaymentGateBundle:RbsRegisterOrderRequest', 'r')
            ->where('r.order = :order')
            ->getQuery();

        $query->setParameter(':order', $order);
        return $query->getOneOrNullResult();
    }

    public function createPayRequest(Order $order, array $extraParameters = array())
    {
        $oldRegisterOrderRequest = $this->getPayRequest($order);
        
        if ($oldRegisterOrderRequest)
            return $oldRegisterOrderRequest;

        $registerOrderRequest = new RbsRegisterOrderRequest();

        $registerOrderRequest->setOrder($order);
        $registerOrderRequest->setMerchantOrderNumber($order->getId());
        $registerOrderRequest->setDescription($order->getDescription());
        $registerOrderRequest->setAmount($order->getAmount() * 100);
        $registerOrderRequest->setSessionTimeoutSecs($this->paySessionTimeoutSecs);
        $registerOrderRequest->setClientId($order->getCustomer()->getId());

        if (key_exists('merchantLogin', $extraParameters))
            $registerOrderRequest->setMerchantLogin($extraParameters['merchantLogin']);
//        if (key_exists('pageView', $extraParameters))
//            $registerOrderRequest->setPageView($extraParameters['pageView']);

        $routeParams = array(
            'pageView' => $registerOrderRequest->getPageView()
        );

        if ($this->environment != 'dev') {
            $registerOrderRequest->setReturnUrl($this->router->generate($this->payReturnRoute, $routeParams, Router::ABSOLUTE_URL));
            if ($this->payFailRoute)
                $registerOrderRequest->setFailUrl($this->router->generate($this->payFailRoute, $routeParams, Router::ABSOLUTE_URL));
        } else {
            if ($this->payReturnRouteDev)
                $registerOrderRequest->setReturnUrl($this->router->generate($this->payReturnRouteDev, $routeParams, Router::ABSOLUTE_URL));
            else
                $registerOrderRequest->setReturnUrl($this->router->generate($this->payReturnRoute, $routeParams, Router::ABSOLUTE_URL));

            if ($this->payFailRouteDev)
                $registerOrderRequest->setReturnUrl($this->router->generate($this->payFailRouteDev, $routeParams, Router::ABSOLUTE_URL));
            else if ($this->payFailRoute)
                $registerOrderRequest->setReturnUrl($this->router->generate($this->payFailRoute, $routeParams, Router::ABSOLUTE_URL));
        }

        $this->em->persist($registerOrderRequest);
        $this->em->flush();

        return $registerOrderRequest;
    }

    /**
     * @param mixed $payReturnRoute
     */
    public function setPayReturnRoute($payReturnRoute)
    {
        $this->payReturnRoute = $payReturnRoute;
    }

    /**
     * @param mixed $payFailRoute
     */
    public function setPayFailRoute($payFailRoute)
    {
        $this->payFailRoute = $payFailRoute;
    }

    /**
     * @param mixed $payReturnRouteDev
     */
    public function setPayReturnRouteDev($payReturnRouteDev)
    {
        $this->payReturnRouteDev = $payReturnRouteDev;
    }

    /**
     * @param mixed $payFailRouteDev
     */
    public function setPayFailRouteDev($payFailRouteDev)
    {
        $this->payFailRouteDev = $payFailRouteDev;
    }

    /**
     * @param mixed $paySessionTimeoutSecs
     */
    public function setPaySessionTimeoutSecs($paySessionTimeoutSecs)
    {
        $this->paySessionTimeoutSecs = $paySessionTimeoutSecs;
    }
}
