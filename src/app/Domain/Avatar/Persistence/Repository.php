<?php

namespace BackToWin\Domain\Avatar\Persistence;

use BackToWin\Domain\Avatar\Entity\Avatar;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;

interface Repository
{
    public function insert(Avatar $avatar): void;

    /**
     * @param Avatar $avatar
     * @return void
     * @throws NotFoundException
     */
    public function update(Avatar $avatar): void;

    /**
     * @param Uuid $userId
     * @return Avatar
     * @throws NotFoundException
     */
    public function get(Uuid $userId): Avatar;
}
