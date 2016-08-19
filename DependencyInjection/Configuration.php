<?php

namespace ItQuasar\C4CoreRbsPaymentGateBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    const PaySessionTimeOutSecs = 'pay_session_timeout_secs';

    const GateUrl = 'gate_url';
    const UserName = 'user_name';
    const Password = 'password';
    const PayReturnRoute = 'pay_return_route';
    const PayFailRoute = 'pay_fail_route';

    const GateUrlDev = 'gate_url_dev';
    const UserNameDev = 'user_name_dev';
    const PasswordDev = 'password_dev';
    const PayReturnRouteDev = 'pay_return_route_dev';
    const PayFailRouteDev = 'pay_fail_route_dev';
    
    private $alias;

    public function __construct($alias)
    {
        $this->alias = $alias;
    }

    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root($this->alias);

        $rootNode->
        children()
            ->scalarNode(self::PaySessionTimeOutSecs)->end()
            
            ->scalarNode(self::GateUrl)->isRequired()->cannotBeEmpty()->end()
            ->scalarNode(self::UserName)->isRequired()->cannotBeEmpty()->end()
            ->scalarNode(self::Password)->isRequired()->cannotBeEmpty()->end()
            ->scalarNode(self::PayReturnRoute)->isRequired()->cannotBeEmpty()->end()
            ->scalarNode(self::PayFailRoute)->isRequired()->end()

            ->scalarNode(self::GateUrlDev)->isRequired()->end()
            ->scalarNode(self::UserNameDev)->isRequired()->end()
            ->scalarNode(self::PasswordDev)->isRequired()->end()
            ->scalarNode(self::PayReturnRouteDev)->isRequired()->end()
            ->scalarNode(self::PayFailRouteDev)->isRequired()->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
