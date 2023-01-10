<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\Common\Exception\InvalidResponseException;

class RefundRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate(
            'amount',
            'transactionReference'
        );

        $baseData = $this->getBaseData();

        return array_merge($baseData, [
            'amount' => $this->getAmount(),
            'payment_reference' => $this->getTransactionReference(),
        ]);
    }

    public function sendData($data): RefundResponse
    {
        try {
            $payment = $this->httpRequest(
                'POST',
                $this->getEndpoint() . '/payments/refund',
                $this->getHeaders(),
                $data
            );

            return $this->response = new RefundResponse(
                $this,
                $payment
            );
        } catch (InvalidResponseException $e) {
            return $this->response = new RefundResponse(
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
