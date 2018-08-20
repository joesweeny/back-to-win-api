<?php

namespace GamePlatform\Domain\Admin\Bank\Services;

use GamePlatform\Domain\Admin\Bank\Bank;
use GamePlatform\Domain\Admin\Bank\Exception\BankingException;
use GamePlatform\Framework\Exception\RepositoryDuplicationException;
use GamePlatform\Domain\Admin\Bank\Persistence\Repository;
use GamePlatform\Domain\Admin\Exception\AdminException;
use GamePlatform\Framework\Uuid\Uuid;
use Money\Money;
use Psr\Log\LoggerInterface;

class FundsHandler
{
    /**
     * @var Repository
     */
    private $repository;
    /**
     * @var Bank
     */
    private $bank;
    /**
     * @var LoggerInterface
     */
    private $logger;


    public function __construct(Repository $repository, Bank $bank, LoggerInterface $logger)
    {
        $this->repository = $repository;
        $this->bank = $bank;
        $this->logger = $logger;
    }

    /**
     * @param Uuid $gameId
     * @param Money $money
     * @throws AdminException
     * @return void
     */
    public function addSettledGameFunds(Uuid $gameId, Money $money): void
    {
        try {
            $this->repository->insert($gameId, $money);

            $this->bank->deposit($gameId, $money);
        } catch (RepositoryDuplicationException | BankingException $e) {
            $this->logError($gameId, $money, $e);

            if ($e instanceof BankingException) {
                $this->repository->delete($gameId);
            }

            throw new AdminException("Unable to adds funds. Message: {$e->getMessage()}");
        }
    }

    private function logError(Uuid $gameId, Money $money, \Exception $e): void
    {
        $currency = $money->getCurrency()->getCode();

        $amount = $money->getAmount();

        $this->logger->error(
            "Unable to settle Admin funds for {$gameId}: Currency '{$currency}' Amount '{$amount}'",
            [
                'exception' => $e
            ]
        );
    }
}
