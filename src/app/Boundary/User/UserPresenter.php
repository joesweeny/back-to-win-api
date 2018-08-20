<?php

namespace GamePlatform\Boundary\User;

use GamePlatform\Domain\User\Entity\User;

class UserPresenter
{
    /**
     * @param User $user
     * @return \stdClass
     * @throws \GamePlatform\Framework\Exception\UndefinedException
     */
    public function toDto(User $user): \stdClass
    {
        return (object) [
            'id' => $user->getId()->__toString(),
            'username' => $user->getUsername(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreatedDate()->format(\DATE_ATOM),
            'updated_at' => $user->getLastModifiedDate()->format(\DATE_ATOM)
        ];
    }
}
