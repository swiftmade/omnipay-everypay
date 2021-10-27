<?php

namespace Omnipay\EveryPay\Enums;

class PaymentState
{
    /**
     * Payment is initiated by a Merchant. Payment method is not selected yet. It is a common state for all types
     * of payments (card, open banking, alternative payments). The customer is supposed to select the payment method
     * and continue the payment flow.
     */
    const INITIAL = 'initial';

    /**
     * Initialization of the payment. A process whereby a card Issuer approves or declines the use of the card for a
     * particular purchase transaction at a merchant. If the authorization is successful the purchase amount will be
     * reserved on the cardholder’s account. In the case of 3DS payments, the authorization process also involves
     * cardholder authentication.
     */
    const AUTHORISED = 'authorised';

    /**
     * For card payments it is settlement of the transaction, the acquirer bank has transferred the funds to the
     * merchant’s bank account. For open banking payments this state is obtained after the customer completes SCA and
     * payment passes all the checks on the bank side. This is the final state. For Paypal payments, it means settlement
     * is completed to the merchant's PayPal account.
     */
    const SETTLED = 'settled';

    /**
     * Payment that failed for technical reasons (either our or processors, i.e 399, 4999 failures) or authorization
     * was declined by a card issuer. This is the final status of the payment.
     */
    const FAILED = 'failed';

    /**
     * This state is used for a case in which payment is confirmed by the user but final confirmation by the bank has
     * not arrived yet. In case of this status, a customer can be returned to the e-shop depending on the merchant’s
     * preference. So, customer return should be treated similarly as a trigger as if callback notification was received
     * and merchants need to check payment status. Until the callback notification for final status is received,
     * merchants should show a proper message to the customer which says payment is in progress.It is possible to test
     * sent for processing status with any open banking payment method in the demo environment. When initiating payment,
     * set the amount to 33 EUR and go through the payment flow like normally. After confirming the payment,
     * it will stay in sent for processing status for 30 seconds and then it will go to final status, normally
     * it will be settled.
     */
    const SENT_FOR_PROCESSING = 'sent_for_processing';

    /**
     * This state is used when the payment requires 3DS authentication.
     */
    const WAITING_FOR_3DS_RESPONSE = 'waiting_for_3ds_response';

    /**
     * For open banking in this state, the IBAN is selected, payment initiation is sent to the bank and the customer
     * is waited to complete SCA. For card payment the 3DS authentication window was prompted.
     */
    const WAITING_FOR_SCA = 'waiting_for_sca';

    /**
     * Intermediate status when 3DS flow is completed but payment is not processed further due to technical errors.
     */
    const CONFIRMED_3DS = 'confirmed_3ds';

    /**
     * This state refers to the case in which a customer does not complete the payment confirmation and abandon the
     * payment. For card payments every customer has 15 minutes to perform 3DS authentication.
     * If the time is exceeded and 3DS authentication is not finalised the payment status is changed from
     * ‘Waiting for 3DS‘ to ‘Abandoned‘. It is Final status and means failed payment.
     */
    const ABANDONED = 'abandoned';

    /**
     * Cancellation of authorization. Void blocks funds transfer for an authorized payment.
     * This is the final status of the payment.
     */
    const VOIDED = 'voided';

    /**
     * Payment is partially or fully refunded, reimbursement of the payment.
     */
    const REFUNDED = 'refunded';

    /**
     * The cardholder has disputed payment and Issuer bank has initiated a chargeback process.
     */
    const CHARGED_BACKED = 'chargebacked';
}
