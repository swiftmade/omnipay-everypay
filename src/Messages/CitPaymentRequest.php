<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\EveryPay\Enums\TokenAgreement;
use Omnipay\Common\Exception\InvalidResponseException;

/**
 * Customer Initiated Transaction(CIT) Payments
 * @see https://support.every-pay.com/downloads/everypay_apiv4_integration_documentation.pdf
 * Page 26
 */
class CitPaymentRequest extends AbstractRequest
{
    /**
     * Payload for the POSt /payments/cit request
     */
    public function getData()
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
            'token_agreement' => TokenAgreement::UNSCHEDULED,
        ];

        if ($this->getEmail()) {
            $data['email'] = $this->getEmail();
        }

        if ($this->getClientIp()) {
            $data['customer_ip'] = $this->getClientIp();
        }

        return array_merge($baseData, $data);
    }

    /**
     * Payload for the POST /payments/charge request
     */
    public function getChargeData($paymentReference)
    {
        $baseData = $this->getBaseData();

        $data = [
            'payment_reference' => $paymentReference,
            'token_details' => [
                'token' => $this->getCardReference(),
            ],
        ];

        return array_merge($baseData, $data);
    }

    public function sendData($data): PurchaseResponse
    {
        try {
            $payment = $this->httpRequest(
                'POST',
                $this->getEndpoint() . '/payments/cit',
                $this->getHeaders(),
                $data
            );

            if ($payment['payment_state'] !== PaymentState::INITIAL) {
                throw new InvalidResponseException(
                    'Unexpected payment state - ' . $payment['payment_state']
                );
            }

            // Ok, we created the payment, let's attempt a charge:
            $charge = $this->httpRequest(
                'POST',
                $this->getEndpoint() . '/payments/charge',
                $this->getHeaders(),
                $this->getChargeData(
                    $payment['payment_reference']
                )
            );

            return $this->response = new PurchaseResponse(
                $this,
                $charge
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
