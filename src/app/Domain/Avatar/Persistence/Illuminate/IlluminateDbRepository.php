<?php

namespace GamePlatform\Domain\Avatar\Persistence\Illuminate;

use GamePlatform\Domain\Avatar\Entity\Avatar;
use GamePlatform\Domain\Avatar\Persistence\Repository;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;
use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;

class IlluminateDbRepository implements Repository
{
    /**
     * @var Connection
     */
    private $connection;
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(Connection $connection, Clock $clock)
    {
        $this->connection = $connection;
        $this->clock = $clock;
    }

    public function insert(Avatar $avatar): void
    {
        $avatar->setCreatedDate($this->clock->now());
        $avatar->setLastModifiedDate($this->clock->now());

        $this->connection->table('avatar')->insert($this->toDatabase($avatar));
    }

    /**
     * @inheritdoc
     */
    public function update(Avatar $avatar): void
    {
        if (!$this->tableWhere($avatar->getUserId())->exists()) {
            throw new NotFoundException("Cannot update Avatar as Avatar with User ID {$avatar->getUserId()} does not exist");
        }

        $avatar->setLastModifiedDate($this->clock->now());

        $this->tableWhere($avatar->getUserId())->update($this->toDatabase($avatar));
    }

    /**
     * @inheritdoc
     */
    public function get(Uuid $userId): Avatar
    {
        if (!$row = $this->tableWhere($userId)->first()) {
            throw new NotFoundException("Avatar with User ID {$userId} does not exist");
        }

        return $this->fromDatabase($row);
    }

    private function tableWhere(Uuid $userId): Builder
    {
        return $this->connection->table('avatar')->where('user_id', $userId->toBinary());
    }

    private function toDatabase(Avatar $avatar): array
    {
        return [
            'user_id' => $avatar->getUserId()->toBinary(),
            'filename' => $avatar->getFilename(),
            'created_at' => $avatar->getCreatedDate()->getTimestamp(),
            'updated_at' => $avatar->getLastModifiedDate()->getTimestamp(),
        ];
    }

    private function fromDatabase(\stdClass $row): Avatar
    {
        return (new Avatar(Uuid::createFromBinary($row->user_id), $row->filename))
            ->setCreatedDate((new \DateTimeImmutable())->setTimestamp($row->created_at))
            ->setLastModifiedDate((new \DateTimeImmutable())->setTimestamp($row->updated_at));
    }
}