<?php

namespace GamePlatform\Boundary\User\Command;

use GamePlatform\Framework\CommandBus\Command;
use GamePlatform\Framework\Uuid\Uuid;

class GetUserByIdCommand implements Command
{
    /**
     * @var Uuid
     */
    private $id;

    /**
     * GetUserByIdCommand constructor.
     * @param string $id
     */
    public function __construct(string $id)
    {
        $this->id = new Uuid($id);
    }

    public function getId(): Uuid
    {
        return $this->id;
    }
}
