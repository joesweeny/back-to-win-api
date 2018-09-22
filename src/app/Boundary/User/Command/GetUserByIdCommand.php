<?php

namespace BackToWin\Boundary\User\Command;

use BackToWin\Framework\CommandBus\Command;
use BackToWin\Framework\Uuid\Uuid;

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
