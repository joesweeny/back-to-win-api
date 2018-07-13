<?php

namespace BackToWin\Domain\User\Persistence\Hydration;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Password\PasswordHash;
use BackToWin\Framework\Uuid\Uuid;

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
            ->setFirstName($data->first_name)
            ->setLastName($data->last_name)
            ->setEmail($data->email)
            ->setLocation($data->location)
            ->setPasswordHash(new PasswordHash($data->password))
            ->setCreatedDate(new \DateTimeImmutable($data->created_at))
            ->setLastModifiedDate(new \DateTimeImmutable($data->updated_at));
    }
}
