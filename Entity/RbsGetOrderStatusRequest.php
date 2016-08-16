<?php
/**
 * Copyright © 2015 Pavel A. Puchkov
 *
 * This file is part of the kino-khv.ru project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ItQuasar\RbsPaymentGateBundle\Entity;


use ItQuasar\C4CoreBundle\Common\GetterSetter;
use ItQuasar\C4CoreBundle\Entity\BankExchangeDocument;

/**
 * Class RbsGetOrderStatusRequest
 * @package ItQuasar\RbsPaymentGateBundle\Entity
 * 
 * @method string getOrderId()
 * @method $this setOrderId(string $orderId)
 * 
 * @method string getLanguage()
 * @method $this setLanguage(string $language)
 */
class RbsGetOrderStatusRequest extends BankExchangeDocument
{
    use GetterSetter;

    protected $orderId;
    
    protected $language;
}