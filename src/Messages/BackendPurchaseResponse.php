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
        $this->request = $request;
        $this->data = $data;
    }

    public function isSuccessful()
    {
        $response = @json_decode($this->data, true);

        if (!is_array($response) || (!isset($response['errors'], $response['charge']))) {
            $this->message = 'Unrecognized response format';
            return false;
        }

        if (isset($response['errors'])) {
            $this->message = $response['errors'][0]['message'];
            return false;
        }

        $charge = $response['charge'];

        if (!in_array($charge['payment_state'], ['authorised', 'settled'])) {
            $this->message = 'Payment has failed - ' . $charge['payment_state'];
            return false;
        }

        if ($charge['order_reference'] !== $this->request->getTransactionId()) {
            $this->message = 'Transaction ID mismatch.';
            return false;
        }

        dd($charge);
        return true;
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
