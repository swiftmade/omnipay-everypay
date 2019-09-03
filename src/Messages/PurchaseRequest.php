<?php
namespace Omnipay\EveryPay\Messages;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount');

        $data = $this->getBaseData();

        return $data;
    }
}
