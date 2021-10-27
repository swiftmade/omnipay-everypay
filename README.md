# Omnipay - EveryPay Gateway

_Disclaimer: This package is **not** an official package by EveryPay AS nor by omnipay._

[EveryPay](https://every-pay.com/) is an Estonian payment provider, currently working with LHV and SEB banks.

The package currently supports a limited set of essential features in EveryPay v4:

- One-off payments
- Requesting card tokens
- One-click / CIT (Customer initiated Transactions) Payments

## Usage

Require the package using composer:

> composer require swiftmade/omnipay-everypay

### Initialize the gateway

```php
$gateway = Omnipay::create('EveryPay')->initialize([
  'username' => '', // EveryPay api username
  'secret' => '', // EveryPay api secret
  'accountName' => '', // merchant account ID
  'testMode' => true, // set to false for production!
  'locale' => 'en', // et=Estonian, see integration guide for more options.
]);
```

### Process a purchase (Gateway)

```php
$purchase = $gateway
    ->purchase(['amount' => $amount])
    ->setTransactionId(uniqid()) // unique order id for this purchase
    ->setClientIp($_SERVER['REMOTE_ADDR']) // optional, helps fraud detection
    ->setEmail('') // optional, helps fraud detection
    ->setReturnUrl($customerUrl); // the url to redirect if the payment fails or gets cancelled

// Uncomment if you want to make the payment using a previously stored card token
// $purchase->setCardReference($token);

// Uncomment if you want to store the card as a token after the payment
// $purchase->setSaveCard(true);

$response = $purchase->send();

// IMPORTANT: Store this payment data somewhere so that we can validate / process it later
$payment = $response->getData();

return $response->redirect(); // this will return a self-submitting html form to EveryPay Gateway API
```

### Complete Payment (handle Gateway redirect from EveryPay)

EveryPay will return to your callback url with a `PUT` request once the payment is finalized.
You need to validate this response and check if the payment succeeded.

```php
// Here, pass the payment array that we previously stored when creating the payment
$response = $gateway->completePurchase(['payment' => $payment])->send();

if (!$response->isSuccessful()) {
  // Payment failed!
  // Check $response->getMessage() for more details.
}

// Payment succeeded!
// Here's your payment reference number: $response->getTransactionReference()

if ($card = $response->getCardToken()) {
  // You also got back a card token
  // Store this somewhere safe for future use!
}
```

### Make a token payment (Backend)

```php
$purchase = $gateway
    ->purchase(['amount' => $amount, 'backend' => true])
    ->setClientIp($_SERVER['REMOTE_ADDR']) // optional, helps fraud detection
    ->setEmail(''); // optional, helps fraud detection

// Pass a valid card token here
$purchase->setCardReference($token);

$response = $purchase->send();

// Store the payment response data if you wish.
$payment = $response->getData();

if ($response->isSuccessful()) {
   // Payment done!
} else {
  // Something went wrong!
  // Check $response->getMessage();
}
```
