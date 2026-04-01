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

You can obtain them from your Finova merchant dashboard.

## Examples

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

## License

This project is licensed under the MIT License.
