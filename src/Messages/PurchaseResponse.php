<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Support\SignedData;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\EveryPay\Concerns\CustomRedirectHtml;
use Omnipay\EveryPay\Support\SignedDataOptions;

/**
 * Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    use CustomRedirectHtml;

    public function __construct(RequestInterface $request, $data)
    {
        $this->request = $request;
        $this->data = $data;
    }

    public function isSuccessful()
    {
        return false;
    }

    public function isRedirect()
    {
        return true;
    }

    public function getRedirectMethod()
    {
        return 'POST';
    }

    public function getRedirectData()
    {
        return SignedData::make(
            $this->data,
            SignedDataOptions::gateway(
                $this->request->getSecret()
            )
        );
    }

    public function getRedirectUrl()
    {
        // TODO: Change this if in production mode!
        return 'https://igw-demo.every-pay.com/transactions/';
    }
}
