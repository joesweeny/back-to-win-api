<?php

namespace GamePlatform\Domain\Avatar\Persistence;

use GamePlatform\Domain\Avatar\Entity\Avatar;
use GamePlatform\Framework\Exception\NotFoundException;
use GamePlatform\Framework\Uuid\Uuid;

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
