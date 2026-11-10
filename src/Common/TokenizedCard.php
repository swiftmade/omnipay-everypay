<?php
namespace Omnipay\EveryPay\Common;

use Omnipay\Common\CreditCard;

class TokenizedCard extends CreditCard
{
    protected $token;

    /**
     *
     * @see https://support.every-pay.com/downloads/everypay_apiv4_integration_documentation.pdf
     * Page 35
     *
     * @param array $payload cc_details
     * @return TokenizedCard
     */
    public static function make(array $payload)
    {
        // Card type. Possible values are ‘visa’ or ‘master_card’.
        $brands = [
            'visa' => self::BRAND_VISA,
            'master_card' => self::BRAND_MASTERCARD,
        ];

        $card = new TokenizedCard(array_filter([
            'brand' => $brands[$payload['type'] ?? ''] ?? null,
            'name' => $payload['holder_name'] ?? null,
            'number' => $payload['last_four_digits'] ?? null,
            'expiryYear' => isset($payload['year']) ? (int) $payload['year'] : null,
            'expiryMonth' => isset($payload['month']) ? (int) $payload['month'] : null,
        ], fn ($value) => $value !== null));

        $card->setToken($payload['token'] ?? null);

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
