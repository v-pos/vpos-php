<?php
    declare(strict_types=1);

    namespace Vpos\VposTest;

    use PHPUnit\Framework\TestCase;
    use Vpos\Vpos;
    use Ramsey\Uuid\Uuid;

    class VposTest extends TestCase 
    {
        private $valid_token = "";

        public function setUp(): void
        {
            $this->valid_token = getenv("MERCHANT_VPOS_TOKEN");
        }

        public function testItShouldGetTransactions() 
        {
            $merchant = new Vpos\Vpos();
            $transactions = $merchant->getTransactions();
            $this->assertIsArray($transactions);
            $this->assertEquals(200, $transactions['status']);
            $this->assertEquals('OK', $transactions['message']);
        }

        public function testItShouldNotGetTransactionsIfTokenIsInvalid() 
        {
            $merchant = new Vpos\Vpos();
            $merchant->setToken("invalid-token");
            $transactions = $merchant->getTransactions();
            $this->assertIsArray($transactions);
            $this->assertEquals(401, $transactions['status']);
            $this->assertEquals('Unauthorized', $transactions['message']);
        }

        public function testItShouldNotGetTransactionIfIdDoesNotExist() 
        {
            $merchant = new Vpos\Vpos();
            $transaction = $merchant->getTransaction(id: "9kOmKYUWxN0Jpe4PBoXzE");
            $this->assertIsArray($transaction);
            $this->assertEquals(404, $transaction['status']);
            $this->assertEquals('Not Found', $transaction['message']);
        }

        public function testItShouldNotGetTransactionByIdIfTokenIsInvalid() 
        {
            $merchant = new Vpos\Vpos();
            $merchant->setToken("invalid-token");
            $transaction = $merchant->getTransaction(id: "9kOmKYUWxN0Jpe4PBoXzE");
            $this->assertIsArray($transaction);
            $this->assertEquals(401, $transaction['status']);
            $this->assertEquals('Unauthorized', $transaction['message']);
        }

        public function testItShouldNotPerformPaymentIfTokenIsInvalid() 
        {
            $merchant = new Vpos\Vpos();
            $merchant->setToken("invalid-token");
            $transaction = $merchant->newPayment(customer: "92588855", amount: "1912.58");
            $this->assertIsArray($transaction);
            $this->assertEquals(401, $transaction['status']);
            $this->assertEquals('Unauthorized', $transaction['message']);
        }

        public function testItShouldNotPerformPaymentIfAmountIsInvalid() 
        {
            $merchant = new Vpos\Vpos();
            $transaction = $merchant->newPayment(customer: "92588855", amount: "invalid");
            $this->assertIsArray($transaction);
            $this->assertEquals(400, $transaction['status']);
            $this->assertEquals('Bad Request', $transaction['message']);
        }

        public function testItShouldNotPerformPaymentIfCustomerIsInvalid() 
        {
            $merchant = new Vpos\Vpos();
            $transaction = $merchant->newPayment(customer: "invalid", amount: "1900.99");
            $this->assertIsArray($transaction);
            $this->assertEquals(400, $transaction['status']);
            $this->assertEquals('Bad Request', $transaction['message']);
        }
    }

?>