<?php
/**
 * Copyright © 2015 Pavel A. Puchkov
 *
 * This file is part of the kino-khv.ru project.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ItQuasar\C4CoreRbsPaymentGateBundle\Twig;


use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsGetOrderStatusRequest;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsGetOrderStatusResponse;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsRegisterOrderRequest;
use ItQuasar\C4CoreRbsPaymentGateBundle\Entity\RbsRegisterOrderResponse;

class TwigInstanceOf extends \Twig_Extension
{
    public function getName()
    {
        return 'c4_core_rbs_payment_gate_instance_of';
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