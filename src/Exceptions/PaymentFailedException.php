<?php
namespace Omnipay\EveryPay\Exceptions;

use Exception;

class PaymentFailed extends Exception implements PaymentException
{
    public function getStatus()
    {
        return 'failed';
    }
}
