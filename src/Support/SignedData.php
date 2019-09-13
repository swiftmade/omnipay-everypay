<?php
namespace Omnipay\EveryPay\Support;

class SignedData
{
    private $data;
    private $options;
    private $excluded;

    public function __construct(array $data, SignedDataOptions $options)
    {
        $this->data = array_merge([], $data);
        $this->options = $options;
        $this->excluded = [];

        ksort($this->data);
        $this->prepareData();
    }

    public static function make(array $data, SignedDataOptions $options)
    {
        return (new self($data, $options))
            ->sign()
            ->toArray();
    }

    public function sign()
    {
        $hmacPayload = [];
        foreach ($this->data as $key => $value) {
            $hmacPayload[] = $key . '=' . $value;
        }

        // Cast hmac payload to string by imploding
        $hmacPayload = implode('&', $hmacPayload);

        // Calculate hmac
        $this->data['hmac'] = hash_hmac(
            'sha1',
            $hmacPayload,
            $this->options->secret()
        );

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
        foreach ($this->data as $field => $value) {
            if (!$this->options->shouldHmacInclude($field)) {
                $this->excluded[$field] = $value;
                unset($this->data[$field]);
            }
        }

        if ($this->options->shouldAppendHmacFields()) {
            $this->appendHmacFields();
        }
    }

    private function appendHmacFields()
    {
        $this->data['hmac_fields'] = null;
        ksort($this->data);

        $this->data['hmac_fields'] = implode(',', array_keys($this->data));
    }
}
