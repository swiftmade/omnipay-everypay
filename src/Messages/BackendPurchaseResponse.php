<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\EveryPay\Concerns\CustomRedirectHtml;

/**
 * Response
 */
class BackendPurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    use CustomRedirectHtml;

    protected $message;

    public function __construct(RequestInterface $request, $data)
    {
        $this->data = $data;
        $this->request = $request;
    }

    public function isSuccessful()
    {
        $response = $this->data['body'];

        if (!is_array($response) || (!isset($response['errors']) && !isset($response['charge']))) {
            $this->message = 'Unrecognized response format';
            return false;
        }

        if (isset($response['errors'])) {
            $this->message = $response['errors'][0]['message'];
            return false;
        }

        $charge = $response['charge'];

        if ($charge['transaction_result'] !== 'completed') {
            $this->message = 'Transaction has failed - ' . $charge['transaction_result'];
            return false;
        }

        if (!in_array($charge['payment_state'], ['authorised', 'settled'])) {
            $this->message = 'Payment has failed - ' . $charge['payment_state'];
            return false;
        }

        if ($charge['order_reference'] !== $this->request->getTransactionId()) {
            $this->message = 'Transaction ID mismatch.';
            return false;
        }

        return true;
    }

    public function getTransactionReference()
    {
        return $this->data['body']['charge']['payment_reference'];
    }

    public function isRedirect()
    {
        return false;
    }

    public function getMessage()
    {
        return $this->message;
    }
}
