<?php

use Omnipay\EveryPay\Gateway;
use Omnipay\Tests\GatewayTestCase;

class EveryPayTest extends GatewayTestCase
{
    use Environment;

    public function setUp()
    {
        parent::setUp();
        $this->loadEnvVars();
        $this->gateway = new Gateway();
    }
}
