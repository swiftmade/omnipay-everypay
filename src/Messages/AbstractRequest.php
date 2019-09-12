<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Concerns\Parameters;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    use Parameters;

    protected $liveEndpoint = 'https://pay.every-pay.eu/transactions/';
    protected $testEndpoint = 'https://igw-demo.every-pay.com/transactions/';

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function getBaseData()
    {
        return [
            'api_username' => $this->getUsername(),
            'account_id' => $this->getAccountId(),
            'nonce' => uniqid(true),
            'timestamp' => time(),
            'customer_url' => $this->getCustomerUrl(),
            'callback_url' => $this->getCallbackUrl(),
        ];
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }

    public function setCustomerUrl($url)
    {
        return $this->setParameter('customerUrl', $url);
    }

    public function getCustomerUrl()
    {
        return $this->getParameter('customerUrl');
    }

    public function setCallbackUrl($url)
    {
        return $this->setParameter('callbackUrl', $url);
    }

    public function getCallbackUrl()
    {
        return $this->getParameter('callbackUrl');
    }
}
