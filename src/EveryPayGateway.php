<?php
namespace Swiftmade\EveryPay;

use Omnipay\Common\AbstractGateway;

class EveryPayGateway extends AbstractGateway
{
    public function getName()
    {
        return 'Every Pay';
    }

    public function getShortName()
    {
        return 'everyPay';
    }

    public function getDefaultParameters()
    {
        return [
            'username' => '',
            'secret' => '',
        ];
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    public function setUsername($username)
    {
        return $this->setParameter('username', $username);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    public function setSecret($secret)
    {
        return $this->setParameter('secret', $secret);
    }
}
