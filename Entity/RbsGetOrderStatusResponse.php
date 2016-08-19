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


use ItQuasar\C4CoreBundle\Common\Castable;
use ItQuasar\C4CoreBundle\Common\GetterSetter;
use ItQuasar\C4CoreBundle\Entity\BankExchangeDocument;

/**
 * Class RbsGetOrderStatusResponse
 * @package ItQuasar\C4CoreRbsPaymentGateBundle\Entity
 *
 * @method int getOrderStatus()
 * @method $this setOrderStatus(int $orderStatus)
 *
 * @method int getErrorCode()
 * @method $this setErrorCode(int $errorCode)
 *
 * @method string getErrorMessage()
 * @method $this setErrorMessage(string $errorMessage)
 *
 * @method string getOrderNumber()
 * @method $this setOrderNumber(string $orderNumber)
 *
 * @method string getPan()
 * @method $this setPan(string $pan)
 *
 * @method string getExpiration()
 * @method $this setExpiration(string $expiration)
 *
 * @method string getCardholderName()
 * @method $this setCardholderName(string $name)
 *
 * @method int getAmount()
 * @method $this setAmount(int $amount)
 *
 * @method string getCurrency()
 * @method $this setCurrency(string $currency)
 *
 * @method string getApprovalCode()
 * @method $this setApprovalCode(string $code)
 *
 * @method string getAuthCode()
 * @method $this setAuthCode(string $code)
 *
 * @method string getIp()
 * @method $this setIp(string $ip)
 *
 * @method string getDate()
 * @method $this setDate(string $date)
 *
 * @method string getOrderDescription()
 * @method $this setOrderDescription(string $description)
 *
 * @method string getActionCodeDescription()
 * @method $this setActionCodeDescription(string $description)
 */
class RbsGetOrderStatusResponse extends BankExchangeDocument
{
    const OrderStatusRegisterNotPayed = 0;
    const Payed = 2;
    const InACS = 5;

    use GetterSetter, Castable;

    protected $orderStatus;

    protected $errorCode;

    protected $errorMessage;

    protected $orderNumber;

    protected $pan;

    protected $expiration;

    protected $cardholderName;

    protected $amount;

    protected $currency;

    protected $approvalCode;

    protected $authCode;

    protected $ip;

    protected $date;

    protected $orderDescription;

    protected $actionCodeDescription;
}