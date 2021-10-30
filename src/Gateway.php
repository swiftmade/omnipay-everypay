<?php

namespace Omnipay\EveryPay;

use Omnipay\Common\AbstractGateway;
use Omnipay\EveryPay\Enums\PaymentType;
use Omnipay\EveryPay\Support\SignedData;
use Omnipay\EveryPay\Messages\CitPaymentRequest;
use Omnipay\EveryPay\Messages\MitPaymentRequest;
use Omnipay\EveryPay\Messages\OneOffPaymentRequest;
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

    public function purchase(array $options = [])
    {
        // By default, create one-off payment.
        $paymentType = $options['paymentType'] ?? PaymentType::ONE_OFF;

        $implementations = [
            PaymentType::ONE_OFF => OneOffPaymentRequest::class,
            PaymentType::CIT => CitPaymentRequest::class,
            PaymentType::MIT => MitPaymentRequest::class,
        ];

        if (! isset($implementations[$paymentType])) {
            throw new \InvalidArgumentException(sprintf(
                'Payment type is not implemented or invalid. (%s) Try one of these: %s',
                $paymentType,
                join(', ', array_keys($implementations))
            ));
        }

        return $this->createRequest(
            $implementations[$paymentType],
            $options
        );
    }

    public function completePurchase(array $options = [])
    {
        return $this->createRequest(CompletePurchaseRequest::class, $options);
    }
}
