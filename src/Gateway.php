<?php
namespace Omnipay\EveryPay;

use Omnipay\Common\AbstractGateway;
use Omnipay\EveryPay\Support\SignedData;
use Omnipay\EveryPay\Messages\PurchaseRequest;
use Omnipay\EveryPay\Messages\BackendPurchaseRequest;
use Omnipay\EveryPay\Messages\CompletePurchaseRequest;

class Gateway extends AbstractGateway
{
    use Concerns\Parameters;

    public function getName()
    {
        return 'Every Pay';
    }

    public function signData(array $data)
    {
        return SignedData::make($data, $this->getSecret());
    }

    public function purchase(array $parameters = [])
    {
        if (isset($parameters['backend']) && $parameters['backend']) {
            return $this->createRequest(BackendPurchaseRequest::class, $parameters);
        }
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }

    public function completePurchase(array $parameters = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $parameters);
    }
}
