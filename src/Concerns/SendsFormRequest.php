<?php
namespace Omnipay\EveryPay\Concerns;

use Omnipay\EveryPay\Support\SignedData;

trait SendsFormRequest
{
    public function sendData($data)
    {
        $url = $this->getEndpoint();

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $response = $this->httpClient->request(
            'POST',
            $url,
            $headers,
            SignedData::make($data, $this->getSecret())->toHttpQuery()
        );

        $data = json_decode($response->getBody(), true);
        file_put_contents('test.html', (string) $response->getBody());

        return $this->createResponse($data);
    }
}
