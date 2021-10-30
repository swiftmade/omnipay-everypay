<?php

use Omnipay\EveryPay\Gateway;
use PHPUnit\Framework\TestCase;
use Omnipay\EveryPay\Enums\PaymentState;
use Omnipay\EveryPay\Common\TokenizedCard;
use Omnipay\EveryPay\Messages\PurchaseResponse;

class PurchaseResponseTest extends TestCase
{
    public function testMissingData()
    {
        $gateway = new Gateway();
        $request = $gateway->purchase();

        $response = new PurchaseResponse(
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

        $response = new PurchaseResponse(
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

    public function testFailsIfTransactionIdsDontMatch()
    {
        $gateway = new Gateway();
        $mockRequest = Mockery::mock($gateway->purchase());
        $mockRequest->setTransactionId('x');

        $response = new PurchaseResponse(
            $mockRequest,
            [
                'order_reference' => 'y',
            ]
        );

        $this->assertEquals('Transaction ID (order_reference) mismatch. Request: x, Response: y', $response->getMessage());
        $this->assertFalse($response->isSuccessful());
    }

    public function testDoesNotRedirectIfPaymentSucceeded()
    {
        $gateway = new Gateway();
        $mockRequest = Mockery::mock($gateway->purchase());
        $mockRequest->setTransactionId('x');

        $response = new PurchaseResponse(
            $mockRequest,
            [
                'order_reference' => 'x',
                'payment_state' => PaymentState::SETTLED,
                'payment_link' => 'abc',
            ]
        );

        $this->assertFalse($response->failed);
        $this->assertFalse($response->isRedirect());
    }

    public function testRedirects()
    {
        $gateway = new Gateway();
        $mockRequest = Mockery::mock($gateway->purchase());
        $mockRequest->setTransactionId('x');

        $response = new PurchaseResponse(
            $mockRequest,
            [
                'order_reference' => 'x',
                'payment_state' => PaymentState::WAITING_FOR_3DS_RESPONSE,
                'payment_link' => 'abc',
            ]
        );

        $this->assertFalse($response->failed);
        $this->assertTrue($response->isRedirect());
    }

    /**
     * @dataProvider successfulPaymentStates
     */
    public function testSuccessful($state)
    {
        $gateway = new Gateway();
        $mockRequest = Mockery::mock($gateway->purchase());
        $mockRequest->setTransactionId('x');

        $response = new PurchaseResponse(
            $mockRequest,
            [
                'order_reference' => 'x',
                'payment_state' => $state,
                'payment_link' => null,
            ]
        );

        $this->assertFalse($response->failed);
        $this->assertTrue($response->isSuccessful());
    }

    public function successfulPaymentStates(): array
    {
        return [
            [PaymentState::SETTLED],
            [PaymentState::AUTHORISED],
        ];
    }

    public function testReturnsTransactionId()
    {
        $gateway = new Gateway();
        $mockRequest = Mockery::mock($gateway->purchase());

        $response = new PurchaseResponse(
            $mockRequest,
            [
                'payment_state' => PaymentState::INITIAL,
                'order_reference' => 'transactionId',
            ]
        );

        $this->assertEquals(
            $response->getTransactionId(),
            'transactionId'
        );
    }

    public function testReturnsTransactionReference()
    {
        $gateway = new Gateway();
        $mockRequest = Mockery::mock($gateway->purchase());

        $response = new PurchaseResponse(
            $mockRequest,
            [
                'payment_state' => PaymentState::INITIAL,
                'payment_reference' => 'X',
            ]
        );

        $this->assertEquals(
            $response->getTransactionReference(),
            'X'
        );
    }

    public function testReturnsTokenizedCard()
    {
        $gateway = new Gateway();
        $mockRequest = Mockery::mock($gateway->purchase());

        $response = new PurchaseResponse(
            $mockRequest,
            [
                'payment_state' => PaymentState::INITIAL,
                'cc_details' => [
                    'token' => '70327e72eb02cf144bd0e4e1',
                    'bin' => '222300',
                    'last_four_digits' => '1381',
                    'month' => '12',
                    'year' => '2025',
                    'holder_name' => 'Every Pay',
                    'type' => 'master_card',
                    'issuer_country' => null,
                    'issuer' => null,
                    'cobrand' => null,
                    'funding_source' => null,
                    'product' => null,
                    'state_3ds' => 'unknown',
                    'authorisation_code' => null,
                ],
            ]
        );

        $card = $response->getTokenizedCard();

        $this->assertInstanceOf(TokenizedCard::class, $card);

        $this->assertEquals(TokenizedCard::BRAND_MASTERCARD, $card->getBrand());
        $this->assertEquals('Every Pay', $card->getName());
        $this->assertEquals('1381', $card->getNumber());
        $this->assertEquals(2025, $card->getExpiryYear());
        $this->assertEquals(12, $card->getExpiryMonth());
        $this->assertEquals('70327e72eb02cf144bd0e4e1', $card->getToken());


        $response = new PurchaseResponse(
            $mockRequest,
            [
                'payment_state' => PaymentState::INITIAL,
                'cc_details' => [
                    'token' => '70327e72eb02cf144bd0e4e1',
                    'type' => 'visa',
                    'month' => '12',
                    'year' => '2025',
                ],
            ]
        );

        $card = $response->getTokenizedCard();
        $this->assertInstanceOf(TokenizedCard::class, $card);
        $this->assertEquals(TokenizedCard::BRAND_VISA, $card->getBrand());
    }

    public function testCardReturnsNullIfTokenIsMissing()
    {
        $gateway = new Gateway();
        $mockRequest = Mockery::mock($gateway->purchase());

        $response = new PurchaseResponse(
            $mockRequest,
            [
                'payment_state' => PaymentState::INITIAL,
                'cc_details' => [
                    'bin' => '222300',
                    'last_four_digits' => '1381',
                    'month' => '12',
                    'year' => '2025',
                    'holder_name' => 'Every Pay',
                    'type' => 'master_card',
                ],
            ]
        );

        $card = $response->getTokenizedCard();
        $this->assertNull($card);
    }
}
