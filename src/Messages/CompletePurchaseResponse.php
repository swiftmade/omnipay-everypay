<?php
namespace Omnipay\EveryPay\Messages;

use Exception;
use Omnipay\EveryPay\Common\CardToken;
use Omnipay\EveryPay\Support\SignedData;
use Omnipay\Common\Message\AbstractResponse;
use Omnipay\EveryPay\Exceptions\PaymentException;
use Omnipay\EveryPay\Exceptions\MismatchException;
use Omnipay\Common\Message\RedirectResponseInterface;
use Omnipay\EveryPay\Exceptions\PaymentFailedException;
use Omnipay\EveryPay\Support\SignedDataOptions;

/**
 * Response
 */
class CompletePurchaseResponse extends AbstractResponse implements RedirectResponseInterface
{
    protected $message;

    protected $successfulStates = [
        'settled',
        'authorised'
    ];

    public function isSuccessful()
    {
        try {
            $this->validateResponse();
        } catch (PaymentException $e) {
            $this->message = sprintf('%s - %s', $e->getStatus(), $e->getMessage());
            return false;
        } catch (Exception $e) {
            $this->message = $e->getMessage();
            return false;
        }

        return true;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function isRedirect()
    {
        return false;
    }

    public function validateResponse()
    {
        if ($this->data['payment']['order_reference'] !== $this->data['request']['order_reference']) {
            throw new MismatchException('Order reference returned by gateway does not match');
        }

        if ($this->everyPayRequestHmac() !== $this->data['request']['hmac']) {
            throw new MismatchException('Invalid HMAC signature in the incoming request');
        }

        if (!in_array($this->data['request']['payment_state'], $this->successfulStates)) {
            throw new PaymentFailedException('Payment has failed.');
        }

        if ($this->data['payment']['amount'] !== $this->data['request']['amount']) {
            throw new MismatchException('Payment amount mismatch');
        }
    }

    public function getCardToken()
    {
        if (!isset($this->data['request']['cc_token'])) {
            return null;
        }
        return CardToken::make($this->data['request']);
    }

    private function everyPayRequestHmac()
    {
        return SignedData::make(
            $this->data['request'],
            SignedDataOptions::gateway(
                $this->request->getSecret()
            )
        )['hmac'];
    }

    public function getTransactionReference()
    {
        return $this->data['request']['payment_reference'];
    }
}
