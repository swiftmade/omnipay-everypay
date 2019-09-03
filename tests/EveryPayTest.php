<?php

use Omnipay\Omnipay;
use Omnipay\EveryPay\Gateway;
use Omnipay\Tests\GatewayTestCase;

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
        Omnipay::create('EveryPay');
    }
}
