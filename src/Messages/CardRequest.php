<?php
namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Concerns\SendsFormRequest;

class CardRequest extends AbstractRequest
{
    use SendsFormRequest;

    public function getData()
    {
        $data = $this->getBaseData();

        $data['amount'] = '0';
        $data['transaction_type'] = 'tokenisation';
        $data['order_reference'] = time();
        $data['callback_url'] = 'https://fairfood.snackhub.ee';
        $data['customer_url'] = 'https://fairfood.snackhub.ee';
        $data['request_cc_token'] = '1';
        $data['token_security'] = 'cvc_3ds';

        return $data;
    }
}
