<?php
namespace Omnipay\EveryPay\Concerns;

trait Parameters
{
    public function getDefaultParameters()
    {
        return [
            'username' => getenv('EVERY_PAY_API_USERNAME'), // api_username
            'secret' => getenv('EVERY_PAY_API_SECRET'), // api_secret
            'accountId' => getenv('EVERY_PAY_ACCOUNT_ID'), // processing account
            'testMode' => true,
            'locale' => 'et',
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

    public function getAccountId()
    {
        return $this->getParameter('accountId');
    }

    public function setAccountId($accountId)
    {
        return $this->setParameter('accountId', $accountId);
    }

    public function getLocale()
    {
        return $this->getParameter('locale');
    }

    public function setLocale($locale)
    {
        return $this->setParameter('locale', $locale);
    }

    public function getClientIp()
    {
        return $this->getParameter('user_ip');
    }

    public function setClientIp($value)
    {
        return $this->setParameter('user_ip', $value);
    }
}
