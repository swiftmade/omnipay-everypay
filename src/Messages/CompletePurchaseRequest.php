<?php

namespace Omnipay\EveryPay\Messages;

use \Omnipay\Common\Exception\InvalidRequestException;

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
            $this->getTransactionReference(),
            $this->getUsername()
        );

        $data = $this->httpRequest(
            'GET',
            $uri,
            $this->getHeaders()
        );

        return $this->response = new PurchaseResponse($this, $data);
    }
}
