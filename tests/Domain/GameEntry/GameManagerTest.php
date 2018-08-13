<?php

namespace BackToWin\Domain\GameEntry;

use BackToWin\Domain\Bank\Bank;
use BackToWin\Domain\Game\Entity\Game;
use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Domain\GameEntry\Entity\GameEntry;
use BackToWin\Domain\GameEntry\Persistence\Repository;
use BackToWin\Domain\GameEntry\Services\EntryFeeStore;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Uuid\Uuid;
use Money\Currency;
use Money\Money;
use PHPUnit\Framework\TestCase;

class GameManagerTest extends TestCase
{
    /** @var  Repository */
    private $repository;
    /** @var  Bank */
    private $bank;
    /** @var  EntryFeeStore */
    private $feeStore;
    /** @var  GameManager */
    private $manager;

    public function setUp()
    {
        $this->repository = $this->prophesize(Repository::class);
        $this->bank = $this->prophesize(Bank::class);
        $this->feeStore = $this->prophesize(EntryFeeStore::class);
        $this->manager = new GameManager(
            $this->repository->reveal(),
            $this->bank->reveal(),
            $this->feeStore->reveal()
        );
    }

    public function test_user_is_added_to_game_if_entry_limit_has_not_been_reached_and_user_has_enough_funds()
    {
        $game = new Game(
            new Uuid('a47eb7ba-1ce7-4f63-9ecb-0d6a9b23fcc2'),
            GameType::GENERAL_KNOWLEDGE(),
            GameStatus::CREATED(),
            new Money(500, new Currency('GBP')),
            new Money(50, new Currency('GBP')),
            new Money(10, new Currency('GBP')),
            new \DateTimeImmutable('2018-07-18 00:00:00'),
            4
        );

        $user = new User('57f08f28-dc80-4adb-bc6b-1cfff1b73d6c');

        $this->repository->get($game->getId())->willReturn(['Game', 'Game']);

        $this->bank->getBalance($user->getId())->willReturn(new Money(5000, new Currency('GBP')));

        $this->repository->insert($game->getId(), $user->getId())->willReturn(
            $entry = new GameEntry($game->getId(), $user->getId())
        );

        $this->bank->withdraw($user->getId(), new Money(500, new Currency('GBP')))->willReturn(
            $fee = new Money(500, new Currency('GBP'))
        );

        $this->feeStore->enter($entry, $fee)->shouldBeCalled();

        $this->manager->addUserToGame($game, $user);
    }
}
