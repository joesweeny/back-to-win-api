<?php

namespace BackToWin\Domain\UserPurse\Persistence\Illuminate;

use BackToWin\Domain\UserPurse\Entity\UserPurse;
use BackToWin\Domain\UserPurse\Entity\UserPurseTransaction;
use BackToWin\Domain\UserPurse\Persistence\Reader;
use BackToWin\Domain\UserPurse\Persistence\Writer;
use BackToWin\Framework\Calculation\Calculation;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\RunsMigrations;
use BackToWin\Testing\Traits\UsesContainer;
use Interop\Container\ContainerInterface;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class IlluminateReaderIntegrationTest extends TestCase
{
    use RunsMigrations,
        UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Writer */
    private $writer;
    /** @var  Reader */
    private $reader;

    public function setUp()
    {
        $this->container = $this->runMigrations($this->createContainer());
        $this->writer = $this->container->get(Writer::class);
        $this->reader = $this->container->get(Reader::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Reader::class, $this->reader);
    }

    public function test_user_purse_can_be_retrieved_from_the_database()
    {
        $this->writer->insert(
            (new UserPurse())
                ->setUserId($id = Uuid::generate())
                ->setTotal(new Money(500, new Currency('GBP')))
        );

        $fetched = $this->reader->getPurse($id);

        $this->assertEquals($id, $fetched->getUserId());
        $this->assertEquals(new Money(500, new Currency('GBP')), $fetched->getTotal());
    }

    public function test_exception_is_thrown_if_user_purse_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Purse for User 511f27c9-58be-49a5-82f1-a8b8807c2075 does not exist');
        $this->reader->getPurse(new Uuid('511f27c9-58be-49a5-82f1-a8b8807c2075'));
    }

    public function test_transaction_can_be_retrieved_by_id()
    {
        $this->writer->insertTransaction(
            (new UserPurseTransaction('f07dc671-63d5-4908-8898-6e138a34b221'))
                ->setUserId(new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'))
                ->setTotal(new Money(500, new Currency('GBP')))
                ->setCalculation(Calculation::ADD())
                ->setDescription('Payment to customer')
        );

        $fetched = $this->reader->getTransaction(new Uuid('f07dc671-63d5-4908-8898-6e138a34b221'));

        $this->assertEquals('f07dc671-63d5-4908-8898-6e138a34b221', $fetched->getId());
        $this->assertEquals('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21', $fetched->getUserId());
        $this->assertEquals(new Money(500, new Currency('GBP')), $fetched->getTotal());
        $this->assertEquals(Calculation::ADD(), $fetched->getCalculation());
        $this->assertEquals('Payment to customer', $fetched->getDescription());
    }

    public function test_exception_is_thrown_if_user_purse_transaction_does_not_exist()
    {
        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Transaction with ID 511f27c9-58be-49a5-82f1-a8b8807c2075 does not exist');
        $this->reader->getTransaction(new Uuid('511f27c9-58be-49a5-82f1-a8b8807c2075'));
    }

    public function test_all_transactions_can_be_retrieved_for_a_user()
    {
        $this->writer->insertTransaction(
            (new UserPurseTransaction(Uuid::generate()))
                ->setUserId(new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'))
                ->setTotal(new Money(500, new Currency('GBP')))
                ->setCalculation(Calculation::ADD())
                ->setDescription('Payment to customer')
        );

        $this->writer->insertTransaction(
            (new UserPurseTransaction(Uuid::generate()))
                ->setUserId(Uuid::generate())
                ->setTotal(new Money(1500, new Currency('GBP')))
                ->setCalculation(Calculation::ADD())
                ->setDescription('Payment to customer')
        );

        $this->writer->insertTransaction(
            (new UserPurseTransaction(Uuid::generate()))
                ->setUserId(new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'))
                ->setTotal(new Money(100, new Currency('GBP')))
                ->setCalculation(Calculation::ADD())
                ->setDescription('Payment to customer')
        );

        $transactions = $this->reader->getTransactionsForUser(new Uuid('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21'));

        $this->assertCount(2, $transactions);

        foreach ($transactions as $transaction) {
            $this->assertEquals('c3dd46d3-f032-4a97-a1bb-e5603a6d3b21', $transaction->getUserId());
        }
    }
}
