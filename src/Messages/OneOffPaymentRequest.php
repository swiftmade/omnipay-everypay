<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\EveryPay\Enums\TokenAgreement;

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

    /**
     * Payload for the POST /payments/charge request
     */
    public function getChargeData($paymentReference): array
    {
        $baseData = $this->getBaseData();

        $data = [
            'payment_reference' => $paymentReference,
        ];

        return array_merge($baseData, $data);
    }

    protected function createResponse($response): OneOffPaymentResponse
    {
        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $body = @json_decode($body, true);

        $data = compact('status', 'body');

        return $this->response = new OneOffPaymentResponse($this, $data);
    }

    public function sendData($data): OneOffPaymentResponse
    {
        $response = $this->httpClient->request(
            'POST',
            $this->getEndpoint() . '/payments/oneoff',
            $this->getHeaders(),
            json_encode($data)
        );

        $payment = @json_decode($response->getBody()->getContents(), true);

        if (! is_array($payment)) {
            return $this->response = OneOffPaymentResponse::error($this, 'Unrecognized response format');
        }

        if (isset($payment['error'])) {
            return $this->response = OneOffPaymentResponse::error(
                $this,
                sprintf(
                    '%d - %s',
                    $payment['error']['code'],
                    $payment['error']['message']
                )
            );
        }

        if ($payment['payment_state'] !== PaymentState::INITIAL) {
            return $this->response = OneOffPaymentResponse::error(
                $this,
                'Unexpected payment state - ' . $payment['payment_state']
            );
        }

        return $this->response = new OneOffPaymentResponse(
            $this,
            $payment
        );
    }
}
