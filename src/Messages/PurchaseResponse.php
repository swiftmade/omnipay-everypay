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

    public function __construct(RequestInterface $request, $data)
    {
        parent::__construct($request, $data);

        $this->validateData();
    }

    protected function validateData()
    {
        $this->message = 'Payment state - ' . $this->data['payment_state'];

        if (! $this->data) {
            $this->fail('Missing data!');
        } elseif (isset($this->data['error'])) {
            $this->fail($this->data['error']['message']);
        } elseif ($this->getTransactionId() !== $this->request->getTransactionId()) {
            $this->fail(sprintf(
                'Transaction ID (order_reference) mismatch. Request: %s, Response: %s',
                $this->request->getTransactionId(),
                $this->getTransactionId()
            ));
        }
    }

    protected function fail($message)
    {
        $this->failed = true;
        $this->message = $message;
    }

    public function getMessage(): ?string
    {
        return $this->message;
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
