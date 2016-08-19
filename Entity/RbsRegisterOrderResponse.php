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
 * Class RbsRegisterOrderResponse
 * @package ItQuasar\C4CoreRbsPaymentGateBundle\Entity
 * 
 * @method string getOrderId()
 * @method $this setOrderId(string $orderId)
 * 
 * @method string getFormUrl()
 * @method $this setFormUrl(string $formUlr)
 * 
 * @method int getErrorCode()
 * @method $this setErrorCode(int $errorCode)
 * 
 * @method string getErrorMessage()
 * @method $this setErrorMessage(string $errorMessage)
 */
class RbsRegisterOrderResponse extends BankExchangeDocument
{
    use GetterSetter, Castable;

    protected $orderId;

    protected $formUrl;

    protected $errorCode;

    protected $errorMessage;
}