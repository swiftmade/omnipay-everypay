<?php
namespace Omnipay\EveryPay\Common;

use Omnipay\Common\CreditCard;

class CardToken extends CreditCard
{
    protected $token;

    public static function make($payload)
    {
        $card = new CardToken([
            'brand' => $payload['cc_type'],
            'name' => $payload['cc_holder_name'],
            'number' => $payload['cc_last_four_digits'],
            'expiryYear' => $payload['cc_year'],
            'expiryMonth' => $payload['cc_month'],
        ]);

        $card->setToken($payload['cc_token']);

        return $card;
    }

    public function setBrand($brand)
    {
        return $this->setParameter('brand', $brand);
    }

    public function getBrand()
    {
        return $this->getParameter('brand');
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }
}
