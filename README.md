# vPOS PHP

<p align="center"><a href="#/"><img src="https://github.com/v-pos/vpos-php/blob/master/assets/logo.png" alt="vPOS"></a></p>

[![](https://img.shields.io/badge/vPOS-OpenSource-blue.svg)](https://www.vpos.ao)

This php library helps you easily interact with the vPOS API,
Allowing merchants apps/services to request a payment from a client through his/her mobile phone number.

The service currently works for solutions listed below:

EMIS - GPO (Multicaixa Express)

Want to know more about how we are empowering merchants in Angola? See our [website](https://vpos.ao)

### Features
- Simple interface
- Uniform plain old objects are returned by all official libraries, so you don't have
to serialize/deserialize the JSON returned by our API.

### Documentation
Does interacting directly with our API service sound better to you? 
See our documentation on [developer.vpos.ao](https://developer.vpos.ao)

## Installation
```php
compose require nextbss/vpos
```

### Configuration
This php library requires you set up the following environment variables on your machine before
interacting with the API using this library:

| Variable | Description | Required |
| --- | --- | --- |
| `GPO_POS_ID` | The Point of Sale ID provided by EMIS | true |
| `GPO_SUPERVISOR_CARD` | The Supervisor card ID provided by EMIS | true |
| `MERCHANT_VPOS_TOKEN` | The API token provided by vPOS | true |
| `PAYMENT_CALLBACK_URL` | The URL that will handle payment notifications | false |
| `REFUND_CALLBACK_URL` | The URL that will handle refund notifications | false |

Don't have this information? [Talk to us](suporte@vpos.ao)

Given you have set up all the environment variables above with the correct information, you will now
be able to authenticate and communicate effectively with our API using this library. 

The next section will show the various payment actions that can be performed by you, the merchant.

### How to instantiate vPOS
To create an instance of a vPOS merchant see argument table and a simple example below. 

#### Constructor Arguments

| Argument | Description | Type |
| --- | --- | --- |
| `token` | Token generated at [vPOS](https://merchant.vpos.ao) dashboard | `string`
| `pos_id` | Merchant POS ID provided by EMIS | `string`
| `supervisor_card` | Merchant Supervisor Card number provided by EMIS | `string`
| `payment_callback_url` | Merchant application JSON endpoint to accept the callback payment response | `string`
| `refund_callback_url` | Merchant application JSON endpoint to accept the callback refund response | `string`

#### Example

```php
$token = "YOUR VPOS TOKEN";
$pos_id = "YOUR GPO POS ID";
$payment_callback_url = "YOUR PAYMENT CALLBACK URL";
$refund_callback_url = "YOUR REFUND CALLBACK URL";
$supervisor_card = "YOUR GPO SUPERVISOR CARD";

$merchant = new Vpos($token, $pos_id, $payment_callback_url, $refund_callback_url, $supervisor_card);
```

### Get a specific Transaction
Retrieves a transaction given a valid transaction ID.

```php
use Vpos\Vpos\Vpos;

$merchant = new Vpos($token, $pos_id, $payment_callback_url, $refund_callback_url, $supervisor_card);
$transaction = $merchant->getTransaction("9kOmKYUWxN0Jpe4PBoXzE");
```

| Argument | Description | Type |
| --- | --- | --- |
| `id` | An existing Transaction ID | `string`

### New Payment Transaction
Creates a new payment transaction given a valid mobile number associated with a `MULTICAIXA` account
and a valid amount.

```php
use Vpos\Vpos\Vpos;

$merchant = new Vpos($token, $pos_id, $payment_callback_url, $refund_callback_url, $supervisor_card);
$payment = $merchant->newPayment(customer: "925889553", amount: "112.58");
```

| Argument | Description | Type |
| --- | --- | --- |
| `mobile` | The mobile number of the client who will pay | `string`
| `amount` | The amount the client should pay, eg. "259.99", "259000.00" | `string`

### Request Refund
Given an existing `parent_transaction_id`, request a refund.

```php
use Vpos\Vpos\Vpos;

$merchant = new Vpos($token, $pos_id, $payment_callback_url, $refund_callback_url, $supervisor_card);
refund = $merchant->newRefund(id: "9kOmKYUWxN0Jpe4PBoXzE");
```

| Argument | Description | Type |
| --- | --- | --- |
| `id` | The ID of transaction you wish to refund | `string`

### Poll Transaction Status
Poll the status of a transaction given a valid `request_id`. 

Note: The `request_id` or simply `id` in this context is essentially the `transaction_id` of an existing request. 

```php
use Vpos\Vpos\Vpos;

$merchant = new Vpos($token, $pos_id, $payment_callback_url, $refund_callback_url, $supervisor_card);
$request = $merchant->getRequest(id: "9kOmKYUWxN0Jpe4PBoXzE");
```

| Argument | Description | Type |
| --- | --- | --- |
| `request_id` | The ID of transaction you wish to poll | `string`

### Have any doubts?
In case of any doubts, bugs, or the like, please leave an issue. We would love to help.

License
----------------

The library is available as open source under the terms of the [MIT License](http://opensource.org/licenses/MIT).