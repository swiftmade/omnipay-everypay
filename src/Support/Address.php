<?php
namespace Omnipay\EveryPay\Support;

class Address
{
    public $address;
    public $postcode;
    public $city;
    public $country;

    public function __construct(array $fields)
    {
        foreach ($fields as $field => $value) {
            $this->$field = $value;
        }
    }

    public static function make($address, $postcode, $city, $country)
    {
        return new Address(compact('address', 'postcode', 'city', 'country'));
    }

    public function toArray()
    {
        return [
            'address' => $this->address,
            'postcode' => $this->postcode,
            'city' => $this->city,
            'country' => $this->country
        ];
    }
}
