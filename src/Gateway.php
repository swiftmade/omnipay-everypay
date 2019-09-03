<?php
namespace Omnipay\EveryPay;

use Omnipay\Common\AbstractGateway;
use Omnipay\EveryPay\Messages\PurchaseRequest;

class Gateway extends AbstractGateway
{
    use Concerns\Parameters;

    public function getName()
    {
        return 'Every Pay';
    }

    public function purchase(array $parameters = [])
    {
        return $this->createRequest(PurchaseRequest::class, $parameters);
    }
}
