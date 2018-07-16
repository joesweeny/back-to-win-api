<?php

namespace BackToWin\Domain\User\Persistence\Hydration;

use BackToWin\Domain\User\Entity\User;

final class Extractor
{
    /**
     * @param User $user
     * @return User|\stdClass
     * @throws \BackToWin\Framework\Exception\UndefinedException
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
