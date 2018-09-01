<?php

namespace GamePlatform\Boundary\Auth\Command\Handlers;

use GamePlatform\Boundary\Auth\Command\ValidateTokenCommand;
use GamePlatform\Domain\Auth\Services\Token\TokenValidator;
use GamePlatform\Framework\Exception\NotAuthenticatedException;

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
