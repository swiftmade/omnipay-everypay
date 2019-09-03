<?php
namespace Omnipay\EveryPay\Concerns;

trait Parameters
{
    public function getDefaultParameters()
    {
        return [
            'username' => getenv('EVERY_PAY_API_USERNAME'),
            'secret' => getenv('EVERY_PAY_API_SECRET'),
            'testMode' => true,
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
