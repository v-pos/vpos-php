<?php
    declare(strict_types=1);

    namespace Vpos\VposTest;

    use PHPUnit\Framework\TestCase;
    use Vpos\Vpos\Vpos;

    class VposTest extends TestCase
    {
        // Get Transactions
        public function testItShouldGetTransactions()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $transactions = $merchant->getTransactions();
            $this->assertIsArray($transactions);
            $this->assertEquals(200, $transactions['status_code']);
            $this->assertEquals('OK', $transactions['message']);
        }

        public function testItShouldNotGetTransactionsIfTokenIsInvalid()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $merchant->setToken("invalid-token");
            $transactions = $merchant->getTransactions();
            $this->assertIsArray($transactions);
            $this->assertEquals(401, $transactions['status_code']);
            $this->assertEquals('Unauthorized', $transactions['message']);
        }

        public function testItShouldNotGetTransactionIfIdDoesNotExist()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            
            $merchant = new Vpos($token, $pos_id, $payment_callback_url, $refund_callback_url, $supervisor_card);
            $transaction = $merchant->getTransaction(id: "9kOmKYUWxN0Jpe4PBoXzE");
            $this->assertIsArray($transaction);
            $this->assertEquals(404, $transaction['status_code']);
            $this->assertEquals('Not Found', $transaction['message']);
        }

        public function testItShouldNotGetTransactionByIdIfTokenIsInvalid()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $merchant->setToken("invalid-token");
            $transaction = $merchant->getTransaction(id: "9kOmKYUWxN0Jpe4PBoXzE");
            $this->assertIsArray($transaction);
            $this->assertEquals(401, $transaction['status_code']);
            $this->assertEquals('Unauthorized', $transaction['message']);
        }

        // New Payment
        public function testItShouldNotPerformPaymentIfTokenIsInvalid()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $merchant->setToken("invalid-token");
            $transaction = $merchant->newPayment(customer: "92588855", amount: "1912.58");
            $this->assertIsArray($transaction);
            $this->assertEquals(401, $transaction['status_code']);
            $this->assertEquals('Unauthorized', $transaction['message']);
        }

        public function testItShouldNotPerformPaymentIfAmountIsInvalid()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $transaction = $merchant->newPayment(customer: "92588855", amount: "invalid");
            $this->assertIsArray($transaction);
            $this->assertEquals(400, $transaction['status_code']);
            $this->assertEquals('Bad Request', $transaction['message']);
        }

        public function testItShouldNotPerformPaymentIfCustomerIsInvalid()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $transaction = $merchant->newPayment(customer: "invalid", amount: "1900.99");
            $this->assertIsArray($transaction);
            $this->assertEquals(400, $transaction['status_code']);
            $this->assertEquals('Bad Request', $transaction['message']);
        }

        public function testItShouldPerformPayment()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $payment = $merchant->newPayment(customer: "925888553", amount: "112.58");
            $this->assertIsArray($payment);
            $this->assertEquals(202, $payment['status_code']);
            $this->assertEquals('Accepted', $payment['message']);
            $this->assertNotFalse('', $payment['location']);
        }

        // New Refund
        public function testItShouldNotPerformRefundIfTokenIsInvalid()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $merchant->setToken("invalid-token");
            $transaction = $merchant->newRefund(id: "non-existent-transaction-id");
            $this->assertIsArray($transaction);
            $this->assertEquals(401, $transaction['status_code']);
            $this->assertEquals('Unauthorized', $transaction['message']);
        }

        public function testItShouldNotPerformRefundIfIdDoesNotExist()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $transaction = $merchant->newRefund(id: "non-existent-transaction-id");
            $this->assertIsArray($transaction);
            $this->assertEquals(202, $transaction['status_code']);
            $this->assertEquals('Accepted', $transaction['message']);
        }

        // Poll Resource
        public function testItShouldNotGetRequestByIdIfTokenIsInvalid()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);
            $merchant->setToken("invalid-token");
            $request = $merchant->getRequest(id: "9kOmKYUWxN0Jpe4PBoXzE");
            $this->assertIsArray($request);
            $this->assertEquals(401, $request['status_code']);
            $this->assertEquals('Unauthorized', $request['message']);
        }

        public function testItShouldGetRequestById()
        {
            $token = getenv("MERCHANT_VPOS_TOKEN");
            $pos_id = getenv("GPO_POS_ID");
            $supervisor_card = getenv("GPO_SUPERVISOR_CARD");
            $payment_callback_url = getenv("PAYMENT_CALLBACK_URL");
            $refund_callback_url = getenv("REFUND_CALLBACK_URL");

            $merchant = new Vpos($token, $pos_id, $supervisor_card, $payment_callback_url, $refund_callback_url);

            $payment = $merchant->newPayment(customer: "925888553", amount: "112.58");

            $id = $merchant->getRequestId($payment);

            $request = $merchant->getRequest(id: $id);
            $this->assertIsArray($request);
            $this->assertEquals(200, $request['status_code']);
            $this->assertEquals('OK', $request['message']);
            $this->assertNotEmpty($request['data']);
        }
    }
