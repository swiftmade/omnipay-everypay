<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\EveryPay\Enums\TokenAgreement;

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

    protected function createResponse($response)
    {
        $status = $response->getStatusCode();
        $body = $response->getBody()->getContents();
        $body = @json_decode($body, true);

        $data = compact('status', 'body');

        return $this->response = new CitPaymentResponse($this, $data);
    }

    public function sendData($data)
    {
        $response = $this->httpClient->request(
            'POST',
            $this->getEndpoint() . '/payments/cit',
            $this->getHeaders(),
            json_encode($data)
        );

        $payment = @json_decode($response->getBody()->getContents(), true);

        if (! is_array($payment)) {
            return $this->response = CitPaymentResponse::error($this, 'Unrecognized response format');
        }

        if (isset($payment['error'])) {
            return $this->response = CitPaymentResponse::error(
                $this,
                sprintf(
                    '%d - %s',
                    $payment['error']['code'],
                    $payment['error']['message']
                )
            );
        }

        if ($payment['payment_state'] !== PaymentState::INITIAL) {
            return $this->response = CitPaymentResponse::error(
                $this,
                'Unexpected payment state - ' . $payment['payment_state']
            );
        }

        // Ok, we created the payment, let's attempt a charge:
        $response = $this->httpClient->request(
            'POST',
            $this->getEndpoint() . '/payments/charge',
            $this->getHeaders(),
            json_encode($this->getChargeData(
                $payment['payment_reference']
            ))
        );

        return $this->response = new CitPaymentResponse(
            $this,
            @json_decode($response->getBody()->getContents(), true)
        );
    }
}
