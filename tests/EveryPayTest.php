<?php

use Omnipay\EveryPay\Gateway;
use Omnipay\Tests\GatewayTestCase;

class EveryPayTest extends GatewayTestCase
{
    use Environment;

    public function setUp(): void
    {
        parent::setUp();
        $this->loadEnvVars();
        $this->gateway = new Gateway();
    }

    public function testAccountName()
    {
        $this->assertSame($this->gateway, $this->gateway->setAccountName('EUR3D1'));
        $this->assertSame('EUR3D1', $this->gateway->getAccountName());
    }

    public function testEmail()
    {
        $this->assertSame($this->gateway, $this->gateway->setEmail('test@test.com'));
        $this->assertSame('test@test.com', $this->gateway->getEmail());
    }
}
