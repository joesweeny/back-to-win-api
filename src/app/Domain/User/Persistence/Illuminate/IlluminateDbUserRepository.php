<?php

namespace Cocktales\Domain\User\Persistence;

use Cocktales\Domain\User\Entity\User;
use Cocktales\Domain\User\Hydration\Extractor;
use Cocktales\Domain\User\Hydration\Hydrator;
use Cocktales\Framework\DateTime\Clock;
use Cocktales\Framework\Exception\NotFoundException;
use Cocktales\Framework\Exception\UserRepositoryException;
use Cocktales\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class IlluminateDbUserRepository implements Repository
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
     * IlluminateDbUserRepository constructor.
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
    public function getUserByEmail(string $email): User
    {
        $data = $this->table()->where('email', $email)->get()->first();

        if (!$data) {
            throw new NotFoundException("User with email '{$email}' does not exist");
        }

        return Hydrator::fromRawData($data);
    }

    /**
     * @inheritdoc
     */

    public function getUserByUsername(string $username): User
    {
        $data = $this->table()->where('username', $username)->get()->first();

        if (!$data) {
            throw new NotFoundException("User with username '{$username}' does not exist");
        }

        return Hydrator::fromRawData($data);
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
    public function getUsers(): Collection
    {
        return Collection::make($this->table()->get()->sortBy('email'))->map(function ($data) {
            return Hydrator::fromRawData($data);
        });
    }

    /**
     * @inheritdoc
     */
    public function deleteUser(User $user)
    {
        $this->table()->where('id', $user->getId()->toBinary())->delete();
    }

    /**
     * @param Uuid $id
     * @return User
     * @throws NotFoundException
     */
    public function getUserById(Uuid $id): User
    {
        $data = $this->table()->where('id', $id->toBinary())->get()->first();

        if (!$data) {
            throw new NotFoundException("User with ID '{$id}' does not exist");
        }

        return Hydrator::fromRawData($data);
    }

    /**
     * @return Builder
     */
    private function table(): Builder
    {
        return $this->connection->table('user');
    }
}
