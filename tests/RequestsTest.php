<?php

use Omnipay\Tests\TestCase;
use Omnipay\EveryPay\Gateway;
use Omnipay\EveryPay\Enums\PaymentType;
use Omnipay\EveryPay\Messages\VoidRequest;
use Omnipay\EveryPay\Messages\RefundRequest;
use Omnipay\EveryPay\Messages\CaptureRequest;
use Omnipay\EveryPay\Messages\CitPaymentRequest;
use Omnipay\EveryPay\Messages\MitPaymentRequest;
use Omnipay\EveryPay\Messages\OneOffPaymentRequest;
use Omnipay\EveryPay\Messages\CompletePurchaseRequest;

class RequestsTest extends TestCase
{
    public function testPurchaseRequest()
    {
        $gateway = new Gateway();

        $this->assertInstanceOf(OneOffPaymentRequest::class, $gateway->purchase());

        $this->assertInstanceOf(OneOffPaymentRequest::class, $gateway->purchase([
            'paymentType' => PaymentType::ONE_OFF,
        ]));

        $this->assertInstanceOf(CitPaymentRequest::class, $gateway->purchase([
            'paymentType' => PaymentType::CIT,
        ]));

        $this->assertInstanceOf(MitPaymentRequest::class, $gateway->purchase([
            'paymentType' => PaymentType::MIT,
        ]));
    }

    public function testCompletePurchaseRequest()
    {
        $gateway = new Gateway();

        $this->assertInstanceOf(CompletePurchaseRequest::class, $gateway->completePurchase());
        $this->assertInstanceOf(CompletePurchaseRequest::class, $gateway->completeAuthorize());
    }

    public function testCaptureRequest()
    {
        $gateway = new Gateway();

        $this->assertInstanceOf(CaptureRequest::class, $gateway->capture());
    }

    public function testRefundRequest()
    {
        $gateway = new Gateway();

        $this->assertInstanceOf(RefundRequest::class, $gateway->refund());
    }

    public function testVoidRequest()
    {
        $gateway = new Gateway();

        $this->assertInstanceOf(VoidRequest::class, $gateway->void());
    }
}
