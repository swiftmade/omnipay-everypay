<?php
namespace Omnipay\EveryPay\Support;

class SignedDataOptions
{
    /**
     * @array
     */
    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public static function gateway($secret)
    {
        return new SignedDataOptions([
            'secret' => $secret,
            'appendHmacFields' => true,
            'dontInclude' => ['locale'],
        ]);
    }

    public static function backend($secret)
    {
        return new SignedDataOptions([
            'secret' => $secret,
            'appendHmacFields' => false,
            'dontInclude' => ['locale'],
        ]);
    }

    public function dontInclude(array $fields)
    {
        $this->options['dontInclude'] = array_unique(
            array_merge(
                $this->options['dontInclude'] ?? [],
                $fields
            )
        );
        return $this;
    }

    public function shouldHmacInclude($field)
    {
        return !in_array($field, $this->options['dontInclude']);
    }

    public function shouldAppendHmacFields()
    {
        return $this->options['appendHmacFields'];
    }

    public function secret()
    {
        return $this->options['secret'];
    }
}
