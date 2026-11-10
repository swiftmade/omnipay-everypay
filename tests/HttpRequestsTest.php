<?php

use Omnipay\Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use Omnipay\EveryPay\Gateway;
use Omnipay\EveryPay\Enums\PaymentType;
use Omnipay\EveryPay\Common\TokenizedCard;

class HttpRequestsTest extends TestCase
{
    /** @var Gateway */
    protected $gateway;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->initialize([
            'username' => 'api_user',
            'secret' => 'api_secret',
            'accountName' => 'EUR3D1',
        ]);
    }

    private function queueJson(array $body, int $status = 200): void
    {
        $this->getMockClient()->addResponse(new Response($status, [], json_encode($body)));
    }

    private function sentBody(int $index = 0): array
    {
        return json_decode((string) $this->getMockedRequests()[$index]->getBody(), true);
    }

    public function testVoidSendsThePaymentReferenceAndReason()
    {
        $this->queueJson(['payment_reference' => 'pay_1', 'payment_state' => 'voided']);

        $response = $this->gateway->void([
            'transactionReference' => 'pay_1',
            'voidReason' => 'Customer cancelled',
        ])->send();

        $body = $this->sentBody();

        $this->assertSame('pay_1', $body['payment_reference']);
        $this->assertSame('Customer cancelled', $body['reason']);
        $this->assertSame('api_user', $body['api_username']);
        $this->assertTrue($response->isSuccessful());
    }

    public function testCaptureWithoutTransactionIdSucceeds()
    {
        $this->queueJson(['payment_reference' => 'pay_1', 'order_reference' => 'order_1', 'payment_state' => 'settled']);

        $response = $this->gateway->capture([
            'amount' => '10.00',
            'transactionReference' => 'pay_1',
        ])->send();

        $this->assertTrue($response->isSuccessful());
    }

    public function testCompletePurchaseWithOnlyThePaymentReferenceSucceeds()
    {
        $this->queueJson(['payment_reference' => 'pay 1', 'order_reference' => 'order_1', 'payment_state' => 'settled']);

        $response = $this->gateway->completePurchase(['transactionReference' => 'pay 1'])->send();

        $this->assertTrue($response->isSuccessful());
        $this->assertSame('order_1', $response->getTransactionId());
        $this->assertStringEndsWith('/payments/pay%201?api_username=api_user', (string) $this->getMockedRequests()[0]->getUri());
    }

    public function testCompletePurchaseStillRejectsAMismatchedOrderReference()
    {
        $this->queueJson(['payment_reference' => 'pay_1', 'order_reference' => 'other_order', 'payment_state' => 'settled']);

        $response = $this->gateway->completePurchase([
            'transactionReference' => 'pay_1',
            'transactionId' => 'order_1',
        ])->send();

        $this->assertFalse($response->isSuccessful());
    }

    public function testCompletePurchaseReturnsAFailedResponseOnApiErrors()
    {
        $this->queueJson(['error' => ['code' => 4024, 'message' => 'Payment not found']], 404);

        $response = $this->gateway->completePurchase(['transactionReference' => 'pay_1'])->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Payment not found', $response->getMessage());
        $this->assertSame(4024, $response->getCode());
    }

    public function testCitKeepsThePaymentReferenceWhenTheChargeFails()
    {
        $this->queueJson(['payment_reference' => 'pay_1', 'order_reference' => 'order_1', 'payment_state' => 'initial']);
        $this->queueJson(['error' => ['code' => 4999, 'message' => 'Charge failed']], 422);

        $response = $this->gateway->purchase([
            'paymentType' => PaymentType::CIT,
            'amount' => '10.00',
            'transactionId' => 'order_1',
            'returnUrl' => 'https://example.com/return',
            'cardReference' => 'token_1',
        ])->send();

        $this->assertFalse($response->isSuccessful());
        $this->assertSame('Charge failed', $response->getMessage());
        $this->assertSame('pay_1', $response->getTransactionReference());
    }

    public function testRequestsUseARandomNonce()
    {
        $first = $this->gateway->purchase()->getData()['nonce'];
        $second = $this->gateway->purchase()->getData()['nonce'];

        $this->assertMatchesRegularExpression('/^[0-9a-f]{32}$/', $first);
        $this->assertNotSame($first, $second);
    }

    public function testSignDataNoLongerCrashes()
    {
        $signed = $this->gateway->signData(['amount' => '10.00', 'locale' => 'en']);

        $this->assertArrayHasKey('hmac', $signed);
        $this->assertSame('en', $signed['locale']);
    }

    public function testTokenizedCardToleratesMissingFields()
    {
        $card = TokenizedCard::make(['token' => 'token_1']);

        $this->assertSame('token_1', $card->getToken());
        $this->assertNull($card->getBrand());
        $this->assertNull($card->getExpiryYear());
    }
}
