<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\Common\Exception\InvalidResponseException;

class VoidRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate(
            'transactionReference'
        );

        $baseData = $this->getBaseData();

        return array_merge($baseData, [
            'payment_reference' => $this->getTransactionReference(),
            'reason' => $this->getReason(),
        ]);
    }

    public function sendData($data): PurchaseResponse
    {
        try {
            $payment = $this->httpRequest(
                'POST',
                $this->getEndpoint() . '/payments/void',
                $this->getHeaders(),
                $data
            );

            return $this->response = new PurchaseResponse(
                $this,
                $payment
            );
        } catch (InvalidResponseException $e) {
            return $this->response = new PurchaseResponse(
                $this,
                [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ],
                ]
            );
        }
    }
}
