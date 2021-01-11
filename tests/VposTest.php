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
            $merchant = new Vpos();
            $transactions = $merchant->getTransactions();
            $this->assertIsArray($transactions);
            $this->assertEquals(200, $transactions['status']);
            $this->assertEquals('OK', $transactions['message']);
        }

        public function testItShouldNotGetTransactionsIfTokenIsInvalid() 
        {
            $merchant = new Vpos();
            $merchant->setToken("invalid-token");
            $transactions = $merchant->getTransactions();
            $this->assertIsArray($transactions);
            $this->assertEquals(401, $transactions['status']);
            $this->assertEquals('Unauthorized', $transactions['message']);
        }

        public function testItShouldNotGetTransactionIfIdDoesNotExist() 
        {
            $merchant = new Vpos();
            $transaction = $merchant->getTransaction(id: "9kOmKYUWxN0Jpe4PBoXzE");
            $this->assertIsArray($transaction);
            $this->assertEquals(404, $transaction['status']);
            $this->assertEquals('Not Found', $transaction['message']);
        }

        public function testItShouldNotGetTransactionByIdIfTokenIsInvalid() 
        {
            $merchant = new Vpos();
            $merchant->setToken("invalid-token");
            $transaction = $merchant->getTransaction(id: "9kOmKYUWxN0Jpe4PBoXzE");
            $this->assertIsArray($transaction);
            $this->assertEquals(401, $transaction['status']);
            $this->assertEquals('Unauthorized', $transaction['message']);
        }

        // New Payment
        public function testItShouldNotPerformPaymentIfTokenIsInvalid() 
        {
            $merchant = new Vpos();
            $merchant->setToken("invalid-token");
            $transaction = $merchant->newPayment(customer: "92588855", amount: "1912.58");
            $this->assertIsArray($transaction);
            $this->assertEquals(401, $transaction['status']);
            $this->assertEquals('Unauthorized', $transaction['message']);
        }

        public function testItShouldNotPerformPaymentIfAmountIsInvalid() 
        {
            $merchant = new Vpos();
            $transaction = $merchant->newPayment(customer: "92588855", amount: "invalid");
            $this->assertIsArray($transaction);
            $this->assertEquals(400, $transaction['status']);
            $this->assertEquals('Bad Request', $transaction['message']);
        }

        public function testItShouldNotPerformPaymentIfCustomerIsInvalid() 
        {
            $merchant = new Vpos();
            $transaction = $merchant->newPayment(customer: "invalid", amount: "1900.99");
            $this->assertIsArray($transaction);
            $this->assertEquals(400, $transaction['status']);
            $this->assertEquals('Bad Request', $transaction['message']);
        }

        public function testItShouldPerformPayment() 
        {
            $merchant = new Vpos();
            $payment = $merchant->newPayment(customer: "925888553", amount: "112.58");
            $this->assertIsArray($payment);
            $this->assertEquals(202, $payment['status']);
            $this->assertEquals('Accepted', $payment['message']);
            $this->assertNotFalse('', $payment['location']);
        }

        // New Refund
        public function testItShouldNotPerformRefundIfTokenIsInvalid()
        {
            $merchant = new Vpos();
            $merchant->setToken("invalid-token");
            $transaction = $merchant->newRefund(id: "non-existent-transaction-id");
            $this->assertIsArray($transaction);
            $this->assertEquals(401, $transaction['status']);
            $this->assertEquals('Unauthorized', $transaction['message']);
        }

        public function testItShouldNotPerformRefundIfIdDoesNotExist()
        {
            $merchant = new Vpos();
            $transaction = $merchant->newRefund(id: "non-existent-transaction-id");
            $this->assertIsArray($transaction);
            $this->assertEquals(202, $transaction['status']);
            $this->assertEquals('Accepted', $transaction['message']);
        }

        // Poll Resource
        public function testItShouldNotGetRequestByIdIfTokenIsInvalid()
        {
            $merchant = new Vpos();
            $merchant->setToken("invalid-token");
            $request = $merchant->getRequest(id: "9kOmKYUWxN0Jpe4PBoXzE");
            $this->assertIsArray($request);
            $this->assertEquals(401, $request['status']);
            $this->assertEquals('Unauthorized', $request['message']);
        }

        public function testItShouldGetRequestById()
        {
            $merchant = new Vpos();

            $payment = $merchant->newPayment(customer: "925888553", amount: "112.58");

            $id = $merchant->getRequestId($payment);

            $request = $merchant->getRequest(id: $id);
            $this->assertIsArray($request);
            $this->assertEquals(200, $request['status']);
            $this->assertEquals('OK', $request['message']);
            $this->assertNotEmpty($request['data']);
        }
    }