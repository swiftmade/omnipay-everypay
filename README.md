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
    ->setTransactionId($_GET['payment_reference'])
    ->setTransactionReference($_GET['order_reference'])
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

