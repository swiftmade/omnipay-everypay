<?php

use Omnipay\EveryPay\Gateway;
use PHPUnit\Framework\TestCase;
use Omnipay\EveryPay\Messages\VoidResponse;
use Omnipay\EveryPay\Messages\RefundRequest;

class VoidResponseTest extends TestCase
{
    public function testMissingData()
    {
        $gateway = new Gateway();
        $request = $gateway->purchase();

        $response = new VoidResponse(
            $request,
            null
        );

        $this->assertEquals('Missing data!', $response->getMessage());
        $this->assertFalse($response->isSuccessful());
    }

    public function testFailsIfErrorIsDetected()
    {
        $gateway = new Gateway();
        $request = $gateway->purchase();

        $response = new VoidResponse(
            $request,
            [
                'error' => [
                    'message' => 'API Error',
                    'code' => 500,
                ],
            ]
        );

        $this->assertEquals('API Error', $response->getMessage());
        $this->assertEquals(500, $response->getCode());
        $this->assertFalse($response->isSuccessful());
    }

    public function testSuccessful()
    {
        $gateway = new Gateway();
        /**
         * @var RefundRequest
         */
        $mockRequest = Mockery::mock($gateway->refund());
        $mockRequest->setTransactionReference('x');
        $mockRequest->setAmount(10);

        $response = new VoidResponse(
            $mockRequest,
            [
                'payment_reference' => 'x',
                'payment_state' => 'voided',
            ]
        );

        $this->assertFalse($response->failed);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals('x', $response->getTransactionReference());
    }
}
