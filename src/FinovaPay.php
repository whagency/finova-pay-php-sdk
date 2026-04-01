<?php

namespace Finova\Pay;

/**
 * Finova Pay SDK
 *
 * A PHP SDK for interacting with the Finova Pay API.
 *
 * MIT License
 *
 * @category        FinovaPay
 * @package         Finova\Pay
 * @version         1.0
 * @author          Finova
 * @copyright       Copyright (c) 2026 Finova
 * @license         https://opensource.org/licenses/MIT MIT License
 */

class FinovaPay
{
    const REQUEST_TIMEOUT = 15;

    private string $apiKey;
    private string $apiSecret;
    private string $baseUrl = 'https://api.finova-stage.pro/pay';

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param string|null $baseUrl Custom API base URL
     */
    public function __construct(string $apiKey, string $apiSecret, ?string $baseUrl = null)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->baseUrl = $baseUrl ? rtrim($baseUrl, '/') : $this->baseUrl;
    }

    /**
     * Create a new merchant order
     *
     * @param array $data
     * @return array
     */
    public function createOrder(array $data): array
    {
        return $this->requestSigned('POST', '/api/v1/merchant/orders', $data);
    }

    /**
     * Get an existing order by ID
     *
     * @param string $orderId
     * @return array
     */
    public function getOrder(string $orderId): array
    {
        return $this->request('GET', "/api/v1/checkout/orders/{$orderId}");
    }

    /**
     * Signed request for secure API endpoints
     *
     * @param string $method
     * @param string $path
     * @param array $body
     * @return array
     */
    private function requestSigned(string $method, string $path, array $body = []): array
    {
        $timestamp = (string) round(microtime(true) * 1000);
        $method = strtoupper($method);
        $rawBody = !empty($body) ? json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : '';

        $stringToSign = $timestamp . '.' . $method . '.' . $path . '.' . $rawBody;
        $signature = hash_hmac('sha256', $stringToSign, $this->apiSecret);

        $headers = [
            'X-Api-Key: ' . $this->apiKey,
            'X-Timestamp: ' . $timestamp,
            'X-Signature: ' . $signature,
            'Idempotency-Key: ' . $this->generateKey(),
        ];

        return $this->request($method, $path, $body, $headers);
    }

    /**
     * Generic request handler
     *
     * @param string $method
     * @param string $path
     * @param array $body
     * @param array $headers
     * @return array
     */
    private function request(string $method, string $path, array $body = [], array $headers = []): array
    {
        $url = $this->baseUrl . $path;
        $method = strtoupper($method);
        $isGet = $method === 'GET';

        $headers = array_merge(
            ['Accept: application/json'],
            $isGet ? [] : ['Content-Type: application/json'],
            $headers
        );

        $ch = curl_init($url);

        $options = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => self::REQUEST_TIMEOUT,
            CURLOPT_CONNECTTIMEOUT => self::REQUEST_TIMEOUT,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ];

        if (!$isGet && $body) {
            $options[CURLOPT_POSTFIELDS] = json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($response === false) {
            return [
                'message' => 'Request error: ' . $error,
                'code' => $statusCode
            ];
        }

        $responseDecoded = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [
                'message' => 'Invalid JSON response',
                'code' => $statusCode
            ];
        }

        return $responseDecoded;
    }

    /**
     * Generate a random key for idempotency
     *
     * @return string
     */
    private function generateKey(): string
    {
        return bin2hex(random_bytes(16));
    }
}