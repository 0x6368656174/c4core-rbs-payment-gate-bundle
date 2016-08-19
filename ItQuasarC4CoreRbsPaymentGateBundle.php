<?php

namespace ItQuasar\C4CoreRbsPaymentGateBundle;

use ItQuasar\C4CoreRbsPaymentGateBundle\DependencyInjection\ItQuasarRbsPaymentGateExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ItQuasarC4CoreRbsPaymentGateBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ItQuasarRbsPaymentGateExtension();
        }

        return $this->extension;
    }
}
