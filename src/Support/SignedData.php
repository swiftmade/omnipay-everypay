<?php
namespace Omnipay\EveryPay\Support;

class SignedData
{
    private $data;
    private $excluded;

    protected $dontInclude = ['locale'];

    public function __construct(array $data)
    {
        $this->data = $data;
        $this->excluded = [];
        $this->prepareData();
    }

    public static function make(array $data, $secret)
    {
        return (new SignedData($data))->sign($secret);
    }

    protected function sign($secret)
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

    public function toHttpQuery()
    {
        return http_build_query($this->data);
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
