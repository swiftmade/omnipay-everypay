# Omnipay - EveryPay Gateway

*Disclaimer: This package is **not** an official package by EveryPay AS nor by omnipay.*

[EveryPay](https://every-pay.com/) is an Estonian payment provider, currently working with LHV and SEB banks.

The package currently supports a limited set of essential features:

- Charging through Gateway API
- Requesting card tokens

**WARNING:** Not production ready yet!

- [ ] Add production endpoints
- [ ] Fix remaining todos

## Usage

### Initialize the gateway

```php
$gateway = Omnipay::create('EveryPay')->initialize([
  'username' => '', // EveryPay api username
  'secret' => '', // EveryPay api secret
  'accountId' => '', // merchant account ID
  'testMode' => true, // production mode is not yet supported. coming soon!
  'locale' => 'en', // et=Estonian, see integration guide for more options.
]);
```

### Process a purchase
```php
$purchase = $gateway
    ->purchase(['amount' => $amount])
    ->setClientIp($_SERVER['REMOTE_ADDR']) // optional, helps fraud detection
    ->setEmail('') // optional, helps fraud detection
    ->setCallbackUrl($callbackUrl) // payment callback where payment result will be sent (with PUT)
    ->setCustomerUrl($callbackUrl); // the url to redirect if the payment fails or gets cancelled


// Uncomment if you want to store the card as a token after the payment
// $purchase->setSaveCard(true);

// Uncomment if you want to make the payment using a previously stored card token  
// $purchase->setCardReference($token); 

$response = $purchase->send();

// IMPORTANT: Store this payment data somewhere so that we can validate / process it later
$payment = $response->getData();

return $response->redirect(); // this will return a self-submitting html form to EveryPay Gateway API
```


### Complete Payment

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
