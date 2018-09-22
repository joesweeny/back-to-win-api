<?php

namespace BackToWin\Domain\User\Persistence\Hydration;

use BackToWin\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class HydratorTest extends TestCase
{
    public function test_converts_raw_data_into_user_entity()
    {
        $user = Hydrator::fromRawData((object) [
            'id' => (new Uuid('ec0bff3c-3a9c-4f71-8a31-99936bd39f56'))->toBinary(),
            'username' => 'joesweeny',
            'email' => 'joe@example.com',
            'password' => 'password',
            'created_at' => 1493847540,
            'updated_at' => 1493847540
        ]);

        $this->assertEquals('ec0bff3c-3a9c-4f71-8a31-99936bd39f56', $user->getId()->__toString());
        $this->assertEquals('joesweeny', $user->getUsername());
        $this->assertEquals('joe@example.com', $user->getEmail());
        $this->assertEquals(new \DateTimeImmutable('2017-05-03 21:39:00'), $user->getCreatedDate());
        $this->assertEquals(new \DateTimeImmutable('2017-05-03 21:39:00'), $user->getLastModifiedDate());
    }
}
