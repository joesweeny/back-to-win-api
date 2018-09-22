<?php

namespace BackToWin\Domain\Avatar\Entity;

use BackToWin\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class AvatarTest extends TestCase
{
    public function test_setters_and_getters_on_avatar_object()
    {
        $avatar = new Avatar(new Uuid('fcfa7703-554c-4af6-a1e4-d1013504194e'), 'avatar.jpg');

        $avatar->setFileContents('string file contents');
        $avatar->setCreatedDate(new \DateTimeImmutable('2018-09-19 00:00:00'));
        $avatar->setLastModifiedDate(new \DateTimeImmutable('2018-09-19 00:00:00'));

        $this->assertEquals('fcfa7703-554c-4af6-a1e4-d1013504194e', $avatar->getUserId());
        $this->assertEquals('avatar.jpg', $avatar->getFilename());
        $this->assertEquals('string file contents', $avatar->getFileContents());
        $this->assertEquals(new \DateTimeImmutable('2018-09-19 00:00:00'), $avatar->getCreatedDate());
        $this->assertEquals(new \DateTimeImmutable('2018-09-19 00:00:00'), $avatar->getLastModifiedDate());
    }

    public function test_get_file_contents_returns_null_if_not_set()
    {
        $avatar = new Avatar(new Uuid('fcfa7703-554c-4af6-a1e4-d1013504194e'), 'avatar.jpg');

        $this->assertNull($avatar->getFileContents());
    }
}
