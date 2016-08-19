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

trait WsService
{
    protected $environment;
    
    protected $gateUrl;
    protected $username;
    protected $password;

    protected $gateUrlDev;
    protected $usernameDev;
    protected $passwordDev;
    
    private function createSoap()
    {
        $url = $this->gateUrl;
        $username = $this->username;
        $password = $this->password;
        if ($this->environment == 'dev') {
            $url = $this->gateUrlDev;
            $username = $this->usernameDev;
            $password = $this->passwordDev;
        }

        $soap = new WsSoapClient($url.'?wsdl', array(
            'location' => $url
        ));

        $soap->__setUsernameToken($username, $password);
        
        return $soap;
    }
    
    /**
     * @param mixed $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    
    /**
     * @param mixed $gateUrl
     */
    public function setGateUrl($gateUrl)
    {
        $this->gateUrl = $gateUrl;
    }

    /**
     * @param mixed $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @param mixed $gateUrlDev
     */
    public function setGateUrlDev($gateUrlDev)
    {
        $this->gateUrlDev = $gateUrlDev;
    }

    /**
     * @param mixed $usernameDev
     */
    public function setUsernameDev($usernameDev)
    {
        $this->usernameDev = $usernameDev;
    }

    /**
     * @param mixed $passwordDev
     */
    public function setPasswordDev($passwordDev)
    {
        $this->passwordDev = $passwordDev;
    }
}