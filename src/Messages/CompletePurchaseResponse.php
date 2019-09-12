<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Support\SignedData;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\EveryPay\Exceptions\MismatchException;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\EveryPay\Exceptions\PaymentFailedException;

/**
 * Response
 */
class CompletePurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $successfulStates = [
        'settled',
        'authorised'
    ];

    public function isSuccessful()
    {
        try {
            $this->validateResponse();
        } catch (\Exception $e) {
            $this->status = $e->getStatus();
            $this->reason = $e->getMessage();
            return false;
        }

        return true;
    }

    public function isRedirect()
    {
        return false;
    }

    public function validateResponse()
    {
        if ($this->data['payment']['order_reference'] !== $this->data['request']['order_reference']) {
            throw new MismatchException('Order reference returned by gateway does not match');
        }

        if ($this->everyPayRequestHmac() !== $this->data['request']['hmac']) {
            throw new MismatchException('Invalid HMAC signature in the incoming request');
        }

        if ($this->data['payment']['amount'] !== $this->data['request']['amount']) {
            throw new MismatchException('Payment amount mismatch');
        }

        if (!in_array($this->data['payment']['payment_state'], $this->successfulStates)) {
            throw new PaymentFailedException('Payment has failed.');
        }
    }

    private function everyPayRequestHmac()
    {
        return (new SignedData($this->data['request'], [
            'utf8',
            'hmac',
            '_method',
            'authenticity_token'
        ]))
            ->sign($this->request->getSecret('secret'))
            ->toArray()['hmac'];
    }
}
