<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Support\SignedData;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\EveryPay\Support\SignedDataOptions;
use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Response
 */
class PurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{

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
        return $this->request->getTestMode()
            ? 'https://igw-demo.every-pay.com/transactions/'
            : 'https://pay.every-pay.eu/transactions/';
    }
}
