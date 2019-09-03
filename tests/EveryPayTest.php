<?php

use Omnipay\Tests\GatewayTestCase;
use Swiftmade\EveryPay\EveryPayGateway;

class EveryPayTest extends GatewayTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->gateway = new EveryPayGateway();
    }
}
