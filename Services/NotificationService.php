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
use ItQuasar\C4CoreBundle\AbstractService\Notification;
use ItQuasar\C4CoreBundle\Entity\Order;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsGetOrderStatusRequest;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsGetOrderStatusResponse;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsRegisterOrderResponse;
use Symfony\Component\Translation\Translator;

class NotificationService extends Notification
{
    use WsService;
    
    /** @var  EntityManager */
    protected $em;
    /** @var  Translator */
    protected $translator;
    
    public function __construct(EntityManager $em, Translator $translator)
    {
        $this->em = $em;
        $this->translator = $translator;
    }
    
    public function findPayResponses(Order $order)
    {
        $query = $this->em->createQueryBuilder()
            ->select('r')
            ->from('ItQuasarC4CoreRbsPaymentGateBundle:RbsGetOrderStatusRequest', 'r')
            ->where('r.order = :order')
            ->getQuery();

        $query->setParameter(':order', $order);
        $getOrderStatusRequest = $query->getOneOrNullResult();

        if (!$getOrderStatusRequest) {
            $query = $this->em->createQueryBuilder()
                ->select('r')
                ->from('ItQuasarC4CoreRbsPaymentGateBundle:RbsRegisterOrderResponse', 'r')
                ->andWhere('r.order = :order')
                ->andWhere('r.errorCode = 0')
                ->getQuery();

            $query->setParameter(':order', $order);
            /** @var RbsRegisterOrderResponse $registerOrderResponse */
            $registerOrderResponse = $query->getOneOrNullResult();
            if (!$registerOrderResponse || $registerOrderResponse->getErrorCode() != 0)
                return array();

            $getOrderStatusRequest = new RbsGetOrderStatusRequest();
            $getOrderStatusRequest->setOrder($order);
            $getOrderStatusRequest->setOrderId($registerOrderResponse->getOrderId());

            $this->em->persist($getOrderStatusRequest);
            $this->em->flush();
        }

        $soap = $this->createSoap();

        /** @noinspection PhpUndefinedMethodInspection */
        $soapResult = $soap->getOrderStatus($getOrderStatusRequest);

        $getOrderStatusResponse = new RbsGetOrderStatusResponse($soapResult);

        if ($getOrderStatusResponse->getOrderStatus() == RbsGetOrderStatusResponse::OrderStatusRegisterNotPayed)
            return array();

        return array($getOrderStatusResponse);
    }
}