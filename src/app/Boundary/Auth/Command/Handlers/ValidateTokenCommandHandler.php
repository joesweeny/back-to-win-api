<?php

namespace BackToWin\Boundary\Auth\Command\Handlers;

use BackToWin\Boundary\Auth\Command\ValidateTokenCommand;
use BackToWin\Domain\Auth\Services\Token\TokenValidator;
use BackToWin\Framework\Exception\NotAuthenticatedException;

class ValidateTokenCommandHandler
{
    /**
     * @var TokenValidator
     */
    private $validator;

    public function __construct(TokenValidator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param ValidateTokenCommand $command
     * @return void
     * @throws NotAuthenticatedException
     */
    public function handle(ValidateTokenCommand $command): void
    {
        $this->validator->validate($command->getToken());
    }
}
