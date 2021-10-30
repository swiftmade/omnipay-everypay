<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\EveryPay\Common\TokenizedCard;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $failed = false;
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

        if ($this->getTransactionId() !== $this->request->getTransactionId()) {
            $this->fail(sprintf(
                'Transaction ID (order_reference) mismatch. Request: %s, Response: %s',
                $this->request->getTransactionId(),
                $this->getTransactionId()
            ));

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

        return in_array(
            $this->getPaymentState(),
            [
                PaymentState::AUTHORISED,
                PaymentState::SETTLED,
            ]
        );
    }

    public function isPending(): bool
    {
        if ($this->failed) {
            return false;
        }

        return $this->getPaymentState() === PaymentState::SENT_FOR_PROCESSING;
    }

    public function isRedirect(): bool
    {
        if ($this->failed) {
            return false;
        }

        return ! $this->isSuccessful() && isset($this->data['payment_link']);
    }

    public function getRedirectUrl()
    {
        return $this->data['payment_link'];
    }

    public function isCancelled(): bool
    {
        if ($this->failed) {
            return false;
        }

        return $this->getPaymentState() === PaymentState::ABANDONED;
    }

    public function getTransactionId()
    {
        return $this->data['order_reference'] ?? null;
    }

    public function getTransactionReference()
    {
        return $this->data['payment_reference'] ?? null;
    }

    public function getTokenizedCard(): ?TokenizedCard
    {
        if (! isset($this->data['cc_details'], $this->data['cc_details']['token'])) {
            return null;
        }

        return TokenizedCard::make($this->data['cc_details']);
    }
}
