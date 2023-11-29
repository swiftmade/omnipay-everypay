<?php

namespace Omnipay\EveryPay\Messages;

use Omnipay\EveryPay\Concerns\Parameters;
use Omnipay\Common\Exception\InvalidResponseException;
use Omnipay\Common\Message\AbstractRequest as BaseAbstractRequest;

/**
 * Abstract Request
 *
 */
abstract class AbstractRequest extends BaseAbstractRequest
{
    use Parameters;

    protected $liveEndpoint = 'https://pay.every-pay.eu/api/v4';
    protected $testEndpoint = 'https://igw-demo.every-pay.com/api/v4';

    public function getEndpoint()
    {
        return $this->getTestMode() ? $this->testEndpoint : $this->liveEndpoint;
    }

    protected function getBaseData()
    {
        return [
            /**
             * The api_username of the Merchant sending the request.
             * Must match with username in the Authorization HTTP header.
             */
            'api_username' => $this->getUsername(),

            /**
             * Unique string to prevent replay attacks
             */
            'nonce' => uniqid(true),

            /**
             * The timestamp field represents the time of the request.
             * The request will be rejected if the provided timestamp is outside of an allowed time-window.
             */
            'timestamp' => date('c'),

            /**
             * Locale
             */
            'locale' => $this->getLocale(),
        ];
    }

    protected function getHeaders()
    {
        return [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic ' . base64_encode(
                sprintf('%s:%s', $this->getUsername(), $this->getSecret())
            ),
        ];
    }

    protected function httpRequest($method, $uri, array $headers, $data = null): array
    {
        $response = $this->httpClient->request(
            $method,
            $uri,
            $headers,
            $data ? json_encode($data) : null
        );

        $data = @json_decode($response->getBody()->getContents(), true);

        if (!$data || !is_array($data)) {
            throw new InvalidResponseException(
                'Unrecognized error format.',
                $response->getStatusCode()
            );
        }

        if (isset($data['error'])) {
            throw new InvalidResponseException(
                $data['error']['message'],
                $data['error']['code']
            );
        }

        return $data;
    }

    protected function createResponse($data)
    {
        return $this->response = new Response($this, $data);
    }
}
