<?php

namespace BackToWin\Domain\User\Entity;

use BackToWin\Framework\Password\PasswordHash;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function test_getters_and_setter_methods_on_user_entity()
    {
        $user = (new User('dc5b6421-d452-4862-b741-d43383c3fe1d'))
            ->setUsername('joesweeny')
            ->setEmail('joe@example.com')
            ->setPasswordHash(PasswordHash::createFromRaw('password'))
            ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
            ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'));

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('dc5b6421-d452-4862-b741-d43383c3fe1d', $user->getId()->__toString());
        $this->assertEquals('joesweeny', $user->getUsername());
        $this->assertEquals('joe@example.com', $user->getEmail());
        $this->assertTrue($user->getPasswordHash()->verify('password'));
        $this->assertEquals('2017-05-03 21:39:00', $user->getCreatedDate());
        $this->assertEquals('2017-05-03 21:39:00', $user->getLastModifiedDate());
    }
}
