<?php

namespace BackToWin\Domain\User\Persistence\Illuminate;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\Persistence\Hydration\Extractor;
use BackToWin\Domain\User\Persistence\Writer;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\Exception\NotFoundException;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class IlluminateWriter implements Writer
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Clock
     */
    private $clock;

    /**
     * IlluminateWriter constructor.
     * @param Connection $connection
     * @param Clock $clock
     */
    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    /**
     * @inheritdoc
     */
    public function createUser(User $user): User
    {
        $user->setCreatedDate($this->clock->now());
        $user->setLastModifiedDate($this->clock->now());

        $this->table()->insert((array) Extractor::toRawData($user));

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function updateUser(User $user): User
    {
        if (!$this->table()->where('id', $user->getId()->toBinary())->exists()) {
            throw new NotFoundException("Cannot update - User with User ID {$user->getId()->__toString()} does not exist");
        }

        $user->setLastModifiedDate($this->clock->now());

        $this->table()->where('id', $user->getId()->toBinary())->update((array) Extractor::toRawData($user));

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function deleteUser(User $user)
    {
        $this->table()->where('id', $user->getId()->toBinary())->delete();
    }

    /**
     * @return Builder
     */
    private function table(): Builder
    {
        return $this->connection->table('user');
    }
}
