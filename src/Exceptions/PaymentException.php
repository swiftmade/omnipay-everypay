<?php
namespace Omnipay\EveryPay\Exceptions;

interface PaymentException
{
    public function getStatus();

    public function getMessage();
}
