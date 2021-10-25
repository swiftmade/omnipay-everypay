<?php
namespace Omnipay\EveryPay\Concerns;

trait Parameters
{
    public function getDefaultParameters()
    {
        return [
            'username' => getenv('EVERY_PAY_API_USERNAME'), // api_username
            'secret' => getenv('EVERY_PAY_API_SECRET'), // api_secret
            'accountName' => getenv('EVERY_PAY_ACCOUNT_NAME'), // processing account
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

    public function getAccountName()
    {
        return $this->getParameter('accountName');
    }

    public function setAccountName($accountName)
    {
        return $this->setParameter('accountName', $accountName);
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
