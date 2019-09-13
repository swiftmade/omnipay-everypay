<?php
namespace Omnipay\EveryPay\Messages;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $data = $this->getBaseData();

        $data['amount'] = $this->getAmount();
        $data['order_reference'] = $this->getTransactionId();

        $data['transaction_type'] = 'charge';
        $data['request_cc_token'] = $this->getSaveCard() ? '1' : '0';

        if ($cardReference = $this->getCardReference()) {
            // TODO: Allow setting by parameter
            $data['token_security'] = 'none';
            $data['cc_token'] = $cardReference;
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

    public function sendData($data)
    {
        return $this->createResponse($data);
    }
}
