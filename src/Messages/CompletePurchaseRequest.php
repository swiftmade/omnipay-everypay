<?php
namespace Omnipay\EveryPay\Messages;

class CompletePurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('payment');

        return [
            'payment' => $this->getPayment(),
            'request' => $this->httpRequest->request->all(),
        ];
    }

    public function sendData($data)
    {
        return $this->response = new CompletePurchaseResponse($this, $data);
    }
}
