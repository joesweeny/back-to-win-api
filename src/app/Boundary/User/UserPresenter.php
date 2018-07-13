<?php

namespace BackToWin\Boundary\User;

use BackToWin\Domain\User\Entity\User;

class UserPresenter
{
    /**
     * @param User $user
     * @return \stdClass
     * @throws \BackToWin\Framework\Exception\UndefinedException
     */
    public function toDto(User $user): \stdClass
    {
        return (object) [
            'id' => $user->getId()->__toString(),
            'username' => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'location' => $user->getLocation(),
            'email' => $user->getEmail(),
            'created_at' => $user->getCreatedDate()->format(\DATE_ATOM),
            'updated_at' => $user->getLastModifiedDate()->format(\DATE_ATOM)
        ];
    }
}
