<?php

namespace GamePlatform\Domain\Game\Persistence;

use GamePlatform\Domain\Game\Enum\GameStatus;
use GamePlatform\Domain\Game\Enum\GameType;
use GamePlatform\Framework\Entity\PrivateAttributesTrait;

class GameRepositoryQuery
{
    use PrivateAttributesTrait;

    public function whereStatusEquals(GameStatus $status): self
    {
        return $this->set('where_status_equals', $status);
    }

    public function getWhereStatusEquals(): ?GameStatus
    {
        return $this->get('where_status_equals');
    }

    public function whereTypeEquals(GameType $type): self
    {
        return $this->set('where_type_equals', $type);
    }

    public function getWhereTypeEquals(): ?GameType
    {
        return $this->get('where_type_equals');
    }
}
