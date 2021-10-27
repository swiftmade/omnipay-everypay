<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Customer Initiated Transaction(CIT) Payments
 * @see https://support.every-pay.com/downloads/everypay_apiv4_integration_documentation.pdf
 * Page 26
 */
class OneOffPaymentResponse extends AbstractResponse implements RedirectResponseInterface
{
    public $message;

    public static function error(RequestInterface $request, $message): self
    {
        $response = new self($request, null);
        $response->message = $message;

        return $response;
    }

    public function isSuccessful(): bool
    {
        return false;
    }

    public function getTransactionReference()
    {
        return $this->data['body']['charge']['payment_reference'];
    }

    public function isRedirect(): bool
    {
        if (isset($this->data['error'])) {
            return false;
        }

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
