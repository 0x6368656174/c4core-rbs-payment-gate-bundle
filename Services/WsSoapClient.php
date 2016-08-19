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

class WsSoapClient extends \SoapClient
{
    private $username;
    
    /**
     * WS-Security Password
     * @var string
     */
    private $password;
    
    /**
     * Set WS-Security credentials
     *
     * @param string $username
     * @param string $password
     */
    public function __setUsernameToken($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        $this->__setSoapHeaders($this->generateWSSecurityHeader());
    }
    
    /**
     * Generates WS-Security headers
     *
     * @return \SoapHeader
     */
    private function generateWSSecurityHeader()
    {
        $xml = '
<wsse:Security xmlns:wsse="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd" 
xmlns:wsu="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-%20wssecurity-utility-1.0.xsd">
    <wsse:UsernameToken  wsu:Id="UsernameToken-87">
        <wsse:Username>' . $this->username . '</wsse:Username>
        <wsse:Password Type="http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText">' . $this->password . '</wsse:Password>
    </wsse:UsernameToken>
</wsse:Security>
';
        return new \SoapHeader('http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
            'Security',
            new \SoapVar($xml, XSD_ANYXML),
            true
        );
    }
}