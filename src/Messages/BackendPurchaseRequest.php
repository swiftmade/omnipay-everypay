<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Support\SignedData;
use Omnipay\EveryPay\Support\SignedDataOptions;

class BackendPurchaseRequest extends AbstractRequest
{
    protected $liveEndpoint = 'https://gw.every-pay.eu';
    protected $testEndpoint = 'https://gw-demo.every-pay.com';

    public function getData()
    {
        $data = $this->getBaseData();
        unset($data['callback_url'], $data['customer_url']);

        $data['amount'] = $this->getAmount();
        $data['order_reference'] = $this->getTransactionId();

        if ($cardReference = $this->getCardReference()) {
            $data['cc_token'] = $cardReference;
        }

        $data['device_info'] = json_encode([
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ]);

        return $data;
    }

    public function getEmail()
    {
        return $this->getParameter('email');
    }

    public function setEmail($value)
    {
        return $this->setParameter('email', $value);
    }

    protected function signData($envelope, $data)
    {
        return [
            $envelope => SignedData::make(
                $data,
                SignedDataOptions::backend($this->getSecret())
            ),
        ];
    }

    protected function createResponse($response)
    {
        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $body = @json_decode($body, true);

        $data = compact('status', 'body');

        return $this->response = new BackendPurchaseResponse($this, $data);
    }

    public function sendData($data)
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $data = $this->signData('charge', $data);

        $response = $this->httpClient->request(
            'POST',
            $this->getEndpoint() . '/charges',
            $headers,
            json_encode($data)
        );

        return $this->createResponse($response);
    }
}
