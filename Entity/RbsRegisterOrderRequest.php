<?php
/**
 * Copyright © 2015 Pavel A. Puchkov
 *
 * This file is part of the kino-khv.ru project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ItQuasar\C4CoreRbsPaymentGateBundle\Entity;

use ItQuasar\C4CoreBundle\Common\GetterSetter;
use ItQuasar\C4CoreBundle\Entity\BankExchangeDocument;

/**
 * Class RbsRegisterOrderRequest
 * @package ItQuasar\C4CoreRbsPaymentGateBundle\Entity
 *
 * @method int getId()
 *
 * @method string getMerchantOrderNumber()
 * @method $this setMerchantOrderNumber(string $number)
 * 
 * @method string getDescription()
 * @method $this setDescription(string $description)
 * 
 * @method int getAmount()
 * @method $this setAmount(int $amount)
 * 
 * @method string getCurrency()
 * @method $this setCurrency(string $currency)
 * 
 * @method string getLanguage()
 * @method $this setLanguage(string $language)
 *
 * @method string getPageView()
 * @method $this setPageView(string $pageView)
 * 
 * @method int getBindingId()
 * @method $this setBindingId(int $bindingId)
 * 
 * @method string getExpirationDate()
 * @method $this setExpirationDate(string $expirationDate)
 *
 * @method int getSessionTimeoutSecs()
 * @method $this setSessionTimeoutSecs(int $timeout)
 * 
 * @method string getReturnUrl()
 * @method $this setReturnUrl(string $returnUrl)
 * 
 * @method string getFailUrl()
 * @method $this setFailUrl(string $failUrl)
 * 
 * @method array getParams()
 * @method $this setParams(array $params)
 *
 * @method int getClientId()
 * @method $this setClientId(int $clientId)
 *
 * @method string getMerchantLogin()
 * @method $this setMerchantLogin(string $login)
 */
class RbsRegisterOrderRequest extends BankExchangeDocument
{
    use GetterSetter;

    const PaveViewDesktop = 'DESKTOP';
    const PaveViewMobile = 'MOBILE';

    protected $merchantOrderNumber;

    protected $description;

    protected $amount;

    protected $currency;

    protected $language;

    protected $pageView;

    protected $sessionTimeoutSecs;
    
    protected $bindingId;

    protected $expirationDate;

    protected $returnUrl;

    protected $failUrl;

    protected $params;

    protected $clientId;

    protected $merchantLogin;
    
    private $email;
    private $orderBundle;
    private $features;
    private $taxSystem;
    private $autocompletionDate;
}
