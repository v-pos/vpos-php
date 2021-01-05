<?php
    declare(strict_types=1);

    namespace Vpos\VposTest;

    use PHPUnit\Framework\TestCase;
    use Vpos\Vpos;

    class VposTest extends TestCase 
    {
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
            putenv("MERCHANT_VPOS_TOKEN=invalid");
            $merchant = new Vpos\Vpos();
            $transactions = $merchant->getTransactions();
            $this->assertIsArray($transactions);
            $this->assertEquals(401, $transactions['status']);
            $this->assertEquals('Unauthorized', $transactions['message']);
        }
    }

?>