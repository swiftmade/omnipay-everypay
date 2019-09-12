<?php
namespace Omnipay\EveryPay\Exceptions;

use Exception;

class PaymentFailedException extends Exception implements PaymentException
{
    public function getStatus()
    {
        return 'failed';
    }
}
