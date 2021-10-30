<?php

use Omnipay\EveryPay\Gateway;
use Omnipay\Tests\GatewayTestCase;

class EveryPayTest extends GatewayTestCase
{
    use Environment;

    /** @var Gateway */
    protected $gateway;

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

    public function testSaveCard()
    {
        // Default is false
        $this->assertSame(false, $this->gateway->getSaveCard());

        $this->assertSame($this->gateway, $this->gateway->setSaveCard(true));
        $this->assertSame(true, $this->gateway->getSaveCard());
    }
}
