<?php

namespace BackToWin\Domain\Admin;

use BackToWin\Domain\Admin\Bank\Bank;
use BackToWin\Domain\Admin\Bank\Exception\BankingException;
use BackToWin\Domain\Admin\Bank\Exception\RepositoryDuplicationException;
use BackToWin\Domain\Admin\Bank\Persistence\Repository;
use BackToWin\Domain\Admin\Exception\AdminException;
use BackToWin\Framework\Uuid\Uuid;
use Money\Money;

class AdminManager
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Bank
     */
    private $bank;


    public function __construct(Repository $repository, Bank $bank)
    {
        $this->repository = $repository;
        $this->bank = $bank;
    }

    /**
     * @param Uuid $gameId
     * @param Money $money
     * @throws AdminException
     * @return void
     */
    public function addFunds(Uuid $gameId, Money $money): void
    {
        try {
            $this->repository->insert($gameId, $money);

            $this->bank->deposit($gameId, $money);
        } catch (RepositoryDuplicationException | BankingException $e) {
            throw new AdminException("Unable to adds funds. Message: {$e->getMessage()}");
        }
    }
}
