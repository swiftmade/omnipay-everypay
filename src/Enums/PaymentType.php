<?php

namespace Omnipay\EveryPay\Enums;

class PaymentType
{
    /**
     * When the Payment Service User (PSU) completes the purchase and proceeds with the payment,
     * a merchant initiates payment with(/payments/oneoff) to us.
     *s
     * As a response to this request, payment link with payment methods are provided.
     * PSU is redirected to payment link to complete the payment.
     */
    const ONE_OFF = 'one-off';

    /**
     * MIT's are token-based payments governed by an agreement between the cardholder and merchant that, once set up,
     * allows the merchant to initiate subsequent payments from the card without any direct involvement of the cardholder.
     * As the cardholder is not present when an MIT is performed, the cardholder authentication is not performed.
     */
    const MIT = 'mit';

    /**
     * As in MIT payments, previously stored credentials are used for this type of payment.
     * The main difference with MIT payments is that the customer actively participates in the transaction
     * (like one-click-payments). Also as a response, a payment link is provided to complete the payment which means
     * PSU involvement might be required as well as 3DS authentication.
     */
    const CIT = 'cit';
}
