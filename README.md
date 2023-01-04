[![Latest Version on Packagist](https://img.shields.io/packagist/v/swiftmade/omnipay-everypay.svg?style=flat-square)](https://packagist.org/packages/swiftmade/omnipay-everypay)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Total Downloads](https://img.shields.io/packagist/dt/swiftmade/omnipay-everypay.svg?style=flat-square)](https://packagist.org/packages/swiftmade/omnipay-everypay)

# PHP EveryPay Client (for Omnipay)

Use this package to integrate EveryPay into your PHP application using [Omnipay](http://omnipay.thephpleague.com).

EveryPay is a payment gateway currently used by:

- LHV
- SEB
- Swedbank

The package supports the following payment types:

- One-off payments
- Requesting card tokens
- One-click / CIT (Customer Initiated Transactions) Payments
- MIT (Merchant Initiated Transactions) Payments

## Usage

Install the package using composer

```bash
composer require swiftmade/omnipay-everypay
```

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

### One-off Purchase

```php
$purchase = $gateway
    ->purchase([
        'amount' => $amount,
        'paymentType' => PaymentType::ONE_OFF,
    ])
    ->setTransactionId($orderId) // unique order id for this purchase
    ->setReturnUrl($customerUrl) // the url to redirect if the payment fails or gets cancelled
    ->setClientIp($_SERVER['REMOTE_ADDR']) // optional, helps fraud detection
    ->setEmail(''); // optional, helps fraud detection

// Use this, if you want to make the payment using a previously stored card token
// Only applicable for MIT and CIT payment types.
$purchase->setCardReference($token);

// Uncomment if you want to store the card as a token after the payment
// (Only supported with One-off payment type)
$purchase->setSaveCard(true);

$response = $purchase->send();

// IMPORTANT: Store this payment data somewhere so that we can validate / process it later
$payment = $response->getData();

return $response->redirect(); // this will return a self-submitting html form to EveryPay Gateway API
```

### Customer Initiated Transaction (One-click payment)

```php
$purchase = $gateway
    ->purchase([
        'amount' => $amount,
        'paymentType' => PaymentType::CIT,
    ])
    ->setTransactionId($orderId) // unique order id for this purchase
    ->setCardReference('previously stored card token')
    ->setReturnUrl($customerUrl)
    ->setClientIp($_SERVER['REMOTE_ADDR']) // optional, helps fraud detection
    ->setEmail(''); // optional, helps fraud detection

$response = $purchase->send();

// Store the payment response data if you wish.
$payment = $response->getData();

if ($response->isSuccessful()) {
   // Payment done!
} else if($response->isRedirect()) {
   // 3DS Confirmation needed!
   // Redirect the user to 3DS Page.
   return $response->redirect();
} else {
  // Something went wrong!
  // Check $response->getMessage();
}
```

### Complete Payment (handle Gateway redirect from EveryPay)

EveryPay will redirect the user to the `returnUrl` once the payment is finalized.
You need to validate whether the payment went through.

```php
// Here, pass the payment array that we previously stored when creating the payment
$response = $gateway->completePurchase()
    // These values are passed back to you by EveryPay
    ->setTransactionId($_GET['order_reference'])
    ->setTransactionReference($_GET['payment_reference'])
    ->send();

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

### Authorize & Capture Later

In EveryPay, when the payment will be captured is configured at the account level. If you want to authorize a payment without capturing it, then you need a Merchant Account configured accordingly.

To authorize a payment, simply substitue `purchase` and `completePurchase` methods with `authorize` and `completeAuthorize`. Then call `capture` to capture the funds.

EveryPay will redirect the user to the `returnUrl` once the payment is finalized. You need to validate whether the payment went through.

```php
// Here, pass the payment array that we previously stored when creating the payment
$gateway->authorize([
        'amount' => $amount,
        'paymentType' => PaymentType::CIT,
    ])
    ->setCardReference('previously stored card token')
    // Set all the other parameters. See previous examples ...
    ->send();

// Redirect the user to 3DS confirmation as necessary.

// When EveryPay redirects the user back, do this...
// This won't capture the payment yet, but makes sure the authorization is successful.
$authorizeResponse = $gateway->completeAuthorize()
    ->setTransactionId($_GET['order_reference'])
    ->setTransactionReference($_GET['payment_reference'])
    ->send();

// Hold on to this.. You'll use this reference to capture the payment.
$paymentReference = $authorizeResponse->getTransactionReference();

// When you're ready to capture, call:
$response = $gateway->capture([
  'amount' => $amount, // You can capture partially, or the whole amount.
  'transactionReference' => $paymentReference,
])->send();

if ($response->isSuccessful()) {
   // Payment captured!
} else {
  // Something went wrong!
  // Check $response->getMessage();
}
```

---

### Security

If you discover any security related issues, please email hello@swiftmade.co instead of using the issue tracker.

### Disclaimer

This package is **not** an official package by EveryPay AS nor by Omnipay.

### License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
