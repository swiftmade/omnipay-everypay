<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Support\SignedData;
use Omnipay\EveryPay\Support\SignedDataOptions;

class BackendPurchaseRequest extends AbstractRequest
{
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
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
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

    public function sendData($data)
    {
        // TODO: Production
        // $endpoint = 'https://gw.every-pay.eu/';
        $endpoint = 'https://gw-demo.every-pay.com/';

        $payload = [
            'charge' => SignedData::make(
                $data,
                SignedDataOptions::backend($this->getSecret())
            )
        ];

        $response = $this->httpClient->request('POST', sprintf('%s%s', $endpoint, 'charges'), [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], json_encode($payload));

        return $this->response = new BackendPurchaseResponse($this, $response->getBody()->getContents());
    }
}
