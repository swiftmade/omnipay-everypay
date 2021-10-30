<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\EveryPay\Enums\TokenAgreement;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * One-off Payments
 * @see https://support.every-pay.com/downloads/everypay_apiv4_integration_documentation.pdf
 * Page 18
 */
class OneOffPaymentRequest extends AbstractRequest
{
    /**
     * Payload for the POSt /payments/oneoff request
     */
    public function getData(): array
    {
        $baseData = $this->getBaseData();

        $data = [
            /**
             * Processing account used for the payment.
             * Most importantly, this will determine available payment methods and currency of the payment.
             */
            'account_name' => $this->getAccountName(),

            /**
             * URL where the Customer should be redirected after completing the payment.
             * payment_reference and order_reference parameters are added when a customer is redirected to customer_url.
             * Customer URL has to be a fully qualified domain name, it is not possible to use an IP address or localhost.
             */
            'customer_url' => $this->getReturnUrl(),

            'amount' => $this->getAmount(),
            'order_reference' => $this->getTransactionId(),
        ];

        if ($this->getEmail()) {
            $data['email'] = $this->getEmail();
        }

        if ($this->getClientIp()) {
            $data['customer_ip'] = $this->getClientIp();
        }

        if ($this->getSaveCard()) {
            $data['request_token'] = true;
            $data['token_consent_agreed'] = true;
            $data['token_agreement'] = TokenAgreement::UNSCHEDULED;
        }

        return array_merge($baseData, $data);
    }

    public function sendData($data): PurchaseResponse
    {
        try {
            $payment = $this->httpRequest(
                'POST',
                $this->getEndpoint() . '/payments/oneoff',
                $this->getHeaders(),
                $data
            );

            if ($payment['payment_state'] !== PaymentState::INITIAL) {
                throw new InvalidResponseException(
                    'Unexpected payment state - ' . $payment['payment_state']
                );
            }

            return $this->response = new PurchaseResponse(
                $this,
                $payment
            );
        } catch (InvalidResponseException $e) {
            return $this->response = new PurchaseResponse(
                $this,
                [
                    'error' => [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ],
                ]
            );
        }
    }
}
