<?php

namespace BackToWin\Domain\Admin;

use BackToWin\Domain\Admin\Bank\Bank;
use BackToWin\Domain\Admin\Bank\Exception\BankingException;
use BackToWin\Framework\Exception\RepositoryDuplicationException;
use BackToWin\Domain\Admin\Bank\Persistence\Repository;
use BackToWin\Domain\Admin\Exception\AdminException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class AdminManagerTest extends TestCase
{
    /** @var  Repository */
    private $repository;
    /** @var  Bank */
    private $bank;
    /** @var  FundsHandler */
    private $manager;
    /** @var  LoggerInterface */
    private $logger;

    public function setUp()
    {
        $this->repository = $this->prophesize(Repository::class);
        $this->bank = $this->prophesize(Bank::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->manager = new FundsHandler(
            $this->repository->reveal(),
            $this->bank->reveal(),
            $this->logger->reveal()
        );
    }

    public function test_transaction_is_added_to_database_and_funds_added_to_bank()
    {
        $this->repository->insert(
            $id = Uuid::generate(),
            $money = new Money(1000, new Currency('GBP'))
        )->shouldBeCalled();

        $this->bank->deposit($id, $money)->shouldBeCalled();

        $this->manager->addSettledGameFunds($id, $money);

        $this->addToAssertionCount(1);
    }

    public function test_repository_exception_is_caught_and_admin_exception_thrown_if_unable_to_insert_transaction()
    {
        $this->repository->insert(
            $id = Uuid::generate(),
            $money = new Money(1000, new Currency('GBP'))
        )->willThrow($e = new RepositoryDuplicationException('Cannot insert'));

        $this->bank->deposit($id, $money)->shouldNotBeCalled();

        $this->logger->error(
            "Unable to settle Admin funds for {$id}: Currency 'GBP' Amount '1000'", ['exception' => $e]
        )->shouldBeCalled();

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage("Unable to adds funds. Message: {$e->getMessage()}");

        $this->manager->addSettledGameFunds($id, $money);
    }

    public function test_banking_exception_is_caught_and_admin_exception_thrown_if_unable_to_deposit_funds()
    {
        $this->repository->insert(
            $id = Uuid::generate(),
            $money = new Money(1000, new Currency('GBP'))
        )->shouldBeCalled();

        $this->bank->deposit($id, $money)->willThrow($e = new BankingException('Cannot add funds'));

        $this->repository->delete($id)->shouldBeCalled();

        $this->logger->error(
            "Unable to settle Admin funds for {$id}: Currency 'GBP' Amount '1000'", ['exception' => $e]
        )->shouldBeCalled();

        $this->expectException(AdminException::class);
        $this->expectExceptionMessage("Unable to adds funds. Message: {$e->getMessage()}");

        $this->manager->addSettledGameFunds($id, $money);
    }
}
