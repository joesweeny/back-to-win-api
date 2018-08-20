<?php

namespace GamePlatform\Domain\User\Persistence\Hydration;

use GamePlatform\Domain\User\Entity\User;

final class Extractor
{
    /**
     * @param User $user
     * @return \stdClass
     * @throws \GamePlatform\Framework\Exception\UndefinedException
     */
    public static function toRawData(User $user): \stdClass
    {
        return (object) [
            'id' => $user->getId()->toBinary(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'password' => $user->getPasswordHash(),
            'created_at' => $user->getCreatedDate(),
            'updated_at' => $user->getLastModifiedDate()
        ];
    }
}
