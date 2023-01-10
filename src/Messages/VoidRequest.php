<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\Common\Exception\InvalidResponseException;

class VoidRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate(
            'voidReason',
            'transactionReference'
        );

        $baseData = $this->getBaseData();

        return array_merge($baseData, [
            'reason' => $this->getVoidReason(),
            'payment_reference' => $this->getTransactionReference(),
        ]);
    }

    public function sendData($data): VoidResponse
    {
        try {
            $payment = $this->httpRequest(
                'POST',
                $this->getEndpoint() . '/payments/void',
                $this->getHeaders(),
                $data
            );

            return $this->response = new VoidResponse(
                $this,
                $payment
            );
        } catch (InvalidResponseException $e) {
            return $this->response = new VoidResponse(
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
