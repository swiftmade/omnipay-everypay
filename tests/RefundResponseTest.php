<?php

use Omnipay\EveryPay\Gateway;
use PHPUnit\Framework\TestCase;
use Omnipay\EveryPay\Messages\RefundRequest;
use Omnipay\EveryPay\Messages\RefundResponse;

class RefundResponseTest extends TestCase
{
    public function testMissingData()
    {
        $gateway = new Gateway();
        $request = $gateway->purchase();

        $response = new RefundResponse(
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

        $response = new RefundResponse(
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

        $response = new RefundResponse(
            $mockRequest,
            [
                'payment_reference' => 'x',
                'payment_state' => 'refunded',
                'initial_amount' => 10.00,
                'standing_amount' => 5.00,
            ]
        );

        $this->assertFalse($response->failed);
        $this->assertTrue($response->isSuccessful());
        $this->assertEquals(10.00, $response->getInitialAmount());
        $this->assertEquals(5.00, $response->getStandingAmount());
        $this->assertEquals('x', $response->getTransactionReference());
    }
}
