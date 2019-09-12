<?php
namespace Omnipay\EveryPay\Messages;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $data = $this->getBaseData();

        $data['amount'] = $this->getAmount();
        $data['transaction_type'] = 'charge';
        $data['order_reference'] = uniqid('', true);
        $data['token_security'] = 'cvc_3ds';
        $data['request_cc_token'] = $this->getSaveCard() ? '1' : '0';

        return $data;
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
