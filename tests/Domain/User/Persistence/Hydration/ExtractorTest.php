<?php

namespace BackToWin\Domain\User\Persistence\Hydration;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Framework\Password\PasswordHash;
use BackToWin\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class ExtractorTest extends TestCase
{
    public function test_converts_user_entity_into_raw_data()
    {
        $data = Extractor::toRawData(
            (new User('ec0bff3c-3a9c-4f71-8a31-99936bd39f56'))
                ->setUsername('joesweeny')
                ->setEmail('joe@example.com')
                ->setPasswordHash(PasswordHash::createFromRaw('password'))
                ->setCreatedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
                ->setLastModifiedDate(new \DateTimeImmutable('2017-05-03 21:39:00'))
        );

        $this->assertInstanceOf(\stdClass::class, $data);
        $this->assertEquals('ec0bff3c-3a9c-4f71-8a31-99936bd39f56', Uuid::createFromBinary($data->id));
        $this->assertEquals('joesweeny', $data->username);
        $this->assertEquals('joe@example.com', $data->email);
        $this->assertEquals(1493847540, $data->created_at);
        $this->assertEquals(1493847540, $data->updated_at);
    }
}
