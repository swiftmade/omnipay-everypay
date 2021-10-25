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

    protected $liveEndpoint = 'https://pay.every-pay.eu/api/v4';
    protected $testEndpoint = 'https://igw-demo.every-pay.com/api/v4';

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function getBaseData()
    {
        $data = [
            'api_username' => $this->getUsername(),
            'account_name' => $this->getAccountName(),
            'nonce' => uniqid(true),
            'timestamp' => time(),
            'customer_url' => $this->getCustomerUrl(),
        ];

        if ($ip = $this->getClientIp()) {
            $data['user_ip'] = $ip;
        }

        if ($email = $this->getEmail()) {
            $data['email'] = $email;
        }

        return $data;
    }


    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(
                sprintf('%s:%s', $this->getUsername(), $this->getSecret())
            ),
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
}
