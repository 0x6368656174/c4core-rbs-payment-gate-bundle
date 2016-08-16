<?php

namespace ItQuasar\RbsPaymentGateBundle;

use ItQuasar\RbsPaymentGateBundle\DependencyInjection\ItQuasarRbsPaymentGateExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ItQuasarRbsPaymentGateBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ItQuasarRbsPaymentGateExtension();
        }

        return $this->extension;
    }
}
