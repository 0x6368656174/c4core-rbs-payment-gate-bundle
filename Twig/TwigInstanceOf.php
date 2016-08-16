<?php
/**
 * Copyright © 2015 Pavel A. Puchkov
 *
 * This file is part of the kino-khv.ru project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ItQuasar\RbsPaymentGateBundle\Twig;


use ItQuasar\RbsPaymentGateBundle\Entity\RbsGetOrderStatusRequest;
use ItQuasar\RbsPaymentGateBundle\Entity\RbsGetOrderStatusResponse;
use ItQuasar\RbsPaymentGateBundle\Entity\RbsRegisterOrderRequest;
use ItQuasar\RbsPaymentGateBundle\Entity\RbsRegisterOrderResponse;

class TwigInstanceOf extends \Twig_Extension
{
    public function getName()
    {
        return 'rbs_payment_gate_instance_of';
    }

    public function getTests()
    {
        return array(
            new \Twig_SimpleTest('RbsPaymentGateBundle_RegisterOrderRequest', function ($event) { return $event instanceof RbsRegisterOrderRequest; }),
            new \Twig_SimpleTest('RbsPaymentGateBundle_RegisterOrderResponse', function ($event) { return $event instanceof RbsRegisterOrderResponse; }),
            new \Twig_SimpleTest('RbsPaymentGateBundle_GetOrderStatusRequest', function ($event) { return $event instanceof RbsGetOrderStatusRequest; }),
            new \Twig_SimpleTest('RbsPaymentGateBundle_GetOrderStatusResponse', function ($event) { return $event instanceof RbsGetOrderStatusResponse; }),
        );
    }
}