<?php

namespace GamePlatform\Boundary\Avatar;

use GamePlatform\Domain\Avatar\Entity\Avatar;
use GamePlatform\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;

class AvatarPresenterTest extends TestCase
{
    public function test_to_dto_converts_avatar_object_into_scalar_dto()
    {
        $avatar = new Avatar(new Uuid('1f327255-37e1-4738-854e-b92656f90c12'), 'avatar.jpg');

        $avatar->setFileContents('file contents');

        $dto = (new AvatarPresenter())->toDto($avatar);

        $this->assertEquals('1f327255-37e1-4738-854e-b92656f90c12', $dto->user_id);
        $this->assertEquals('avatar.jpg', $dto->filename);
        $this->assertEquals('file contents', $dto->contents);
    }
}
