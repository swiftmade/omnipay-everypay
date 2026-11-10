<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Exception\InvalidResponseException;

class CompletePurchaseRequest extends AbstractRequest
{
    /**
     * @throws InvalidRequestException
     */
    public function getData(): array
    {
        $this->validate('transactionReference');

        return $this->getBaseData();
    }

    public function sendData($data)
    {
        $uri = sprintf(
            '%s/payments/%s?api_username=%s',
            $this->getEndpoint(),
            rawurlencode($this->getTransactionReference()),
            rawurlencode((string) $this->getUsername())
        );

        try {
            $data = $this->httpRequest(
                'GET',
                $uri,
                $this->getHeaders()
            );
        } catch (InvalidResponseException $e) {
            $data = [
                'error' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ],
            ];
        }

        return $this->response = new PurchaseResponse($this, $data);
    }
}
