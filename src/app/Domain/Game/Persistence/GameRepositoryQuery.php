<?php

namespace BackToWin\Domain\Game\Persistence;

use BackToWin\Domain\Game\Enum\GameStatus;
use BackToWin\Domain\Game\Enum\GameType;
use BackToWin\Framework\Entity\PrivateAttributesTrait;
use BackToWin\Framework\Uuid\Uuid;

class GameRepositoryQuery
{
    use PrivateAttributesTrait;

    public function whereIdEquals(Uuid $id): self
    {
        return $this->set('where_id_equals', $id);
    }

    public function getWhereIdEquals(): ?Uuid
    {
        return $this->get('where_id_equals');
    }

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
