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
        }
    }

?>