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

    protected function getEndpoint()
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
            'user_ip' => '127.0.0.1',
        ];
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}
