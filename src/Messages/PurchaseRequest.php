<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Support\Address;

class PurchaseRequest extends AbstractRequest
{
    public function getData()
    {
        $this->validate('amount', 'deliveryAddress');

        $data = $this->getBaseData();
        return $data;
    }

    /**
     * Getters / Setters
     */
    public function getLocale()
    {
        $this->getParameter('locale');
    }

    public function setLocale($locale)
    {
        return $this->setParameter('locale', $locale);
    }

    public function getDeliveryAddress()
    {
        return $this->getParameter('deliveryAddress');
    }

    public function setDeliveryAddress(Address $deliveryAddress)
    {
        return $this->setParameter('deliveryAddress', $deliveryAddress);
    }

    public function getBillingAddress()
    {
        return $this->getParameter('billingAddress');
    }

    public function setBillingAddress(Address $billingAddress)
    {
        return $this->setParameter('billingAddress', $billingAddress);
    }
}
