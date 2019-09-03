<?php
namespace Omnipay\EveryPay;

use Omnipay\Common\AbstractGateway;
use Omnipay\EveryPay\Messages\CardRequest;

class Gateway extends AbstractGateway
{
    use Concerns\Parameters;

    public function getName()
    {
        return 'Every Pay';
    }

    public function createCard(array $parameters = [])
    {
        return $this->createRequest(CardRequest::class, $parameters);
    }
}
