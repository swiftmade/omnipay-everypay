<?php

use Omnipay\Omnipay;
use Omnipay\EveryPay\Gateway;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\EveryPay\Support\Address;

class EveryPayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new Gateway();
    }

    /** @test */
    public function supports_one_off_payments()
    {
        $gateway = Omnipay::create('EveryPay');

        $purchase = $gateway->purchase([
            'amount' => 1,
            'deliveryAddress' => Address::make('Sepapaja 6', '15551', 'Tallinn', 'Estonia')
        ]);

        $purchase->send();
    }
}
