<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\EveryPay\Concerns\CustomRedirectHtml;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Customer Initiated Transaction(CIT) Payments
 * @see https://support.every-pay.com/downloads/everypay_apiv4_integration_documentation.pdf
 * Page 26
 */
class CitPaymentResponse extends AbstractResponse implements RedirectResponseInterface
{
    use CustomRedirectHtml;

    public $message;

    public static function error(RequestInterface $request, $message): self
    {
        $response = new self($request, null);
        $response->message = $message;

        return $response;
    }

    public function isSuccessful(): bool
    {
        if (is_null($this->data)) {
            return false;
        }

        if ($this->data['order_reference'] !== $this->request->getTransactionId()) {
            $this->message = 'Transaction ID (order_reference) mismatch.';
            return false;
        }

        $successfulStates = [
            PaymentState::SETTLED,
            PaymentState::AUTHORISED,
        ];

        if (! in_array($this->data['payment_state'], $successfulStates)) {
            $this->message = 'Payment has failed - ' . $this->data['payment_state'];
            return false;
        }

        return true;
    }

    public function getTransactionReference()
    {
        return $this->data['body']['charge']['payment_reference'];
    }

    public function isRedirect(): bool
    {
        return isset($this->data['payment_link']);
    }

    public function getRedirectUrl()
    {
        return $this->data['payment_link'];
    }

    public function getMessage(): string
    {
        return $this->message;
    }
}
