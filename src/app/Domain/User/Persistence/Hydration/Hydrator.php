<?php

namespace GamePlatform\Domain\User\Persistence\Hydration;

use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Framework\Password\PasswordHash;
use GamePlatform\Framework\Uuid\Uuid;

final class Hydrator
{
    /**
     * @param \stdClass $data
     * @return User
     */
    public static function fromRawData(\stdClass $data): User
    {
        return (new User(Uuid::createFromBinary($data->id)))
            ->setUsername($data->username)
            ->setEmail($data->email)
            ->setPasswordHash(new PasswordHash($data->password))
            ->setCreatedDate((new \DateTimeImmutable)->setTimestamp($data->created_at))
            ->setLastModifiedDate((new \DateTimeImmutable)->setTimestamp($data->updated_at));
    }
}
