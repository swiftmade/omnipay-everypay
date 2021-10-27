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

    /**
     * Customer’s email. Used for Fraud Prevention.
     * @param $email
     * @return \Omnipay\EveryPay\Gateway|\Omnipay\EveryPay\Messages\AbstractRequest
     */
    public function setEmail($email)
    {
        return $this->setParameter('email', $email);
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function getUsername()
    {
        return $this->getParameter('username');
    }

    /**
     * The api_username of the Merchant sending the request.
     * @param $username
     * @return \Omnipay\EveryPay\Gateway|\Omnipay\EveryPay\Messages\AbstractRequest
     */
    public function setUsername($username)
    {
        return $this->setParameter('username', $username);
    }

    public function getSecret()
    {
        return $this->getParameter('secret');
    }

    /**
     * The api_secret of the Merchant sending the request.
     * @param $secret
     * @return \Omnipay\EveryPay\Gateway|\Omnipay\EveryPay\Messages\AbstractRequest
     */
    public function setSecret($secret)
    {
        return $this->setParameter('secret', $secret);
    }

    public function getAccountName()
    {
        return $this->getParameter('accountName');
    }

    /**
     * Processing account used for the payment.
     * Most importantly, this will determine available payment methods and currency of the payment.
     *
     * @param $accountName
     * @return \Omnipay\EveryPay\Gateway|\Omnipay\EveryPay\Messages\AbstractRequest
     */
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

    /**
     * Customer’s IP address. Used for Fraud Prevention.
     * Do not set this to some fixed value, e.g Merchant’s server,
     * as this will start generating false positives in Fraud Check.
     * @param $ip
     * @return \Omnipay\EveryPay\Gateway|\Omnipay\EveryPay\Messages\AbstractRequest
     */
    public function setClientIp($ip)
    {
        return $this->setParameter('user_ip', $ip);
    }
}
