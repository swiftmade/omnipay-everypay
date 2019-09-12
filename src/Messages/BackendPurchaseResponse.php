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

        if (!is_array($response) || !isset($response['charge'])) {
            $this->message = 'Error';
            return false;
        }

        $charge = $response['charge'];
        // TODO: implement

        return false;
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
