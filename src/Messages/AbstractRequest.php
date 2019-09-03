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

    protected $liveEndpoint = 'https://gw.every-pay.eu/';
    protected $testEndpoint = 'https://gw-demo.every-pay.com/';

    protected function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    public function sendData($data)
    {
        $url = $this->getEndpoint() . '?' . http_build_query($data, '', '&');
        $response = $this->httpClient->post($url);
        $data = json_decode($response->getBody(), true);
        return $this->createResponse($data);
    }

    protected function getBaseData()
    {
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}
