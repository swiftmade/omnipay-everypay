<?php
namespace Omnipay\EveryPay\Support;

class SignedData
{
    private $data;
    private $excluded;

    public function __construct(array $data, array $dontInclude = ['locale'])
    {
        $this->data = $data;
        $this->excluded = [];
        $this->dontInclude = $dontInclude;

        $this->prepareData();
    }

    public static function make(array $data, $secret)
    {
        return (new SignedData($data))->sign($secret);
    }

    public function sign($secret)
    {
        $hmacPayload = [];
        foreach ($this->data as $key => $value) {
            $hmacPayload[] = $key . '=' . $value;
        }

        // Cast hmac payload to string by imploding
        $hmacPayload = implode('&', $hmacPayload);

        // Calculate hmac
        $this->data['hmac'] = hash_hmac('sha1', $hmacPayload, $secret);

        // Add back excluded fields (locale)
        foreach ($this->excluded as $key => $value) {
            $this->data[$key] = $value;
        }

        return $this;
    }

    public function toArray()
    {
        return $this->data;
    }

    private function prepareData()
    {
        foreach ($this->dontInclude as $key) {
            if (isset($this->data[$key])) {
                $this->excluded[$key] = $this->data[$key];
                unset($this->data[$key]);
            }
        }

        $this->appendHmacFields();
    }

    private function appendHmacFields()
    {
        $this->data['hmac_fields'] = null;
        ksort($this->data);

        $this->data['hmac_fields'] = implode(',', array_keys($this->data));
    }
}
