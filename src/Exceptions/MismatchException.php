<?php
namespace Omnipay\EveryPay\Exceptions;

use Exception;

class MismatchException extends Exception implements PaymentException
{
    public function getStatus()
    {
        return 'invalid';
    }
}
