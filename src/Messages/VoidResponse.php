<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;

/**
 * Response
 */
class VoidResponse extends AbstractResponse
{
    public $failed = false;

    protected $message = null;
    protected $code = null;

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->validateData();
    }

    protected function validateData()
    {
        if (! $this->data) {
            $this->fail('Missing data!');

            return;
        }

        if (isset($this->data['error'])) {
            $this->fail(
                $this->data['error']['message'],
                $this->data['error']['code'] ?? null
            );

            return;
        }

        $this->message = 'Payment state - ' . $this->data['payment_state'];
    }

    protected function fail($message, $code = null)
    {
        $this->failed = true;
        $this->code = $code;
        $this->message = $message;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function getCode()
    {
        return $this->code;
    }

    protected function getPaymentState()
    {
        return $this->data['payment_state'] ?? null;
    }

    public function isSuccessful(): bool
    {
        if ($this->failed) {
            return false;
        }

        return PaymentState::VOIDED === $this->getPaymentState();
    }

    /**
     * This is the Payment Gateway’s reference to the transaction
     */
    public function getTransactionReference()
    {
        return $this->data['payment_reference'] ?? null;
    }
}
