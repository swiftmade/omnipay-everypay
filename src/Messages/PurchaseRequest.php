<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Support\SignedData;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $data = $this->getBaseData();

        $data['amount'] = $this->getAmount();
        $data['order_reference'] = uniqid('', true);

        if ($this->getUseBackendApi()) {
            unset($data['callback_url'], $data['customer_url']);
        } else {
            $data['transaction_type'] = 'charge';
            $data['request_cc_token'] = $this->getSaveCard() ? '1' : '0';
        }

        if ($cardReference = $this->getCardReference()) {
            $data['token_security'] = 'none';
            $data['cc_token'] = $cardReference;
        }

        if ($ip = $this->getClientIp()) {
            $data['user_ip'] = $ip;
        }

        if ($email = $this->getEmail()) {
            $data['email'] = $email;
        }

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

    public function setUseBackendApi($flag)
    {
        return $this->setParameter('use_backend', $flag);
    }

    public function getUseBackendApi()
    {
        return $this->getParameter('use_backend');
    }

    public function setSaveCard($saveCard)
    {
        return $this->setParameter('save_card', (bool) $saveCard);
    }

    public function getSaveCard()
    {
        return $this->getParameter('save_card');
    }

    protected function createResponse($data)
    {
        return $this->response = new PurchaseResponse($this, $data);
    }

    public function sendDataToBackend($data)
    {
        // TODO: Production
        // $endpoint = 'https://gw.every-pay.eu/';
        $endpoint = 'https://gw-demo.every-pay.com/';

        $data['user_ip'] = $_SERVER['REMOTE_ADDR'];
        $data['device_info'] = json_encode([
            'user_agent' => $_SERVER['HTTP_USER_AGENT']
        ]);
        ksort($data);

        $data = SignedData::withoutHmac($data, $this->getSecret())->toArray();

        $payload = [
            'charge' => $data
        ];

        $response = $this->httpClient->request('POST', sprintf('%s%s', $endpoint, 'charges'), [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ], json_encode($payload));

        return $this->response = new BackendPurchaseResponse($this, $response->getBody()->getContents());
    }

    public function sendData($data)
    {
        if ($this->getUseBackendApi()) {
            return $this->sendDataToBackend($data);
        }
        return $this->response = new PurchaseResponse($this, $data);
    }
}
