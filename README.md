# Finova Pay SDK

A PHP SDK for interacting with the Finova Pay API.

[![Latest Stable Version](https://poser.pugx.org/whagency/finova-pay-php-sdk/v/stable)](https://packagist.org/packages/whagency/finova-pay-php-sdk)
[![License](https://poser.pugx.org/whagency/finova-pay-php-sdk/license)](https://packagist.org/packages/whagency/finova-pay-php-sdk)

## Installation

The preferred way to install this package is via [composer](https://getcomposer.org/download/).

Either run

```
composer require whagency/finova-pay-php-sdk
```

or add to your composer.json file

```json
"require": {
    "whagency/finova-pay-php-sdk": "^1.0"
},
```

## Configuration

You need API credentials to use this SDK:

- API Public Key
- API Private Key
- API Webhook Key

You can obtain them from your Finova merchant dashboard.

## Examples

### `Creating an order`

~~~php
use Finova\Pay\FinovaPay;

$client = new FinovaPay('API_PUBLIC_KEY', 'API_PRIVATE_KEY');

// Create a new merchant order
$response = $client->createOrder([
    'externalOrderId' => '51',
    'assetCode' => 'USDT',
    'amount' => '125.50',
    'title' => 'Demo order',
    'description' => 'Order created from PHP SDK',
    'expiresInSeconds' => 900,
    'successUrl' => 'https://merchant.example/success',
    'pendingUrl' => 'https://merchant.example/pending',
    'failUrl' => 'https://merchant.example/fail',
]);

// Get an existing order by ID
$response = $client->getOrder('MERCHANT_ORDER_ID');
~~~

### `Receiving a POST callback`

~~~php
// Checks the validity of the received request signature
if (FinovaPay::isValidWebhookSignature($body, $headers, 'API_WEBHOOK_KEY')) {

}

// Callback BODY data example
{
    "id": "17",
    "event": "payment.succeeded",
    "createdAt": "2026-05-02T09:47:16Z",
    "data": {
        "settlementReleasedAt": "2026-05-02T09:47:16Z",
        "amount": 10,
        "status": "succeeded",
        "feeAmount": 1.4285714,
        "merchantId": "3a5bcad9-dc38-4565-9286-33925351b16e",
        "occurredAt": "2026-05-02T09:47:16Z",
        "merchantOrderId": "19",
        "settlementStatus": "released",
        "settlementHoldDays": 0,
        "orderId": "23f1beae-7ab5-4ace-8df9-76f1cb9fdbc5",
        "assetCode": "USDT",
        "externalOrderId": "19",
        "settlementReleaseAt": "2026-05-02T09:47:16Z"
    }
}

// Callback HEADERS data example
{
    "Content-Type": "application/json",
    "X-Webhook-Id": "17",
    "X-Webhook-Event": "payment.succeeded",
    "X-Webhook-Timestamp": "1777715236",
    "X-Webhook-Signature": "sha256=bfa9fca98b83104d284f2bfa27ab531f4ec66d073c4cab7e0afd9fcc151db601",
}
~~~

## License

This project is licensed under the MIT License.
