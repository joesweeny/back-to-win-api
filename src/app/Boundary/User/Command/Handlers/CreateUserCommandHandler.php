<?php

namespace GamePlatform\Boundary\User\Command\Handlers;

use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Boundary\User\UserPresenter;
use GamePlatform\Boundary\User\Command\CreateUserCommand;
use GamePlatform\Framework\Exception\UserCreationException;

class CreateUserCommandHandler
{
    /**
     * @var UserOrchestrator
     */
    private $orchestrator;
    /**
     * @var UserPresenter
     */
    private $presenter;

    public function __construct(UserOrchestrator $orchestrator, UserPresenter $presenter)
    {
        $this->orchestrator = $orchestrator;
        $this->presenter = $presenter;
    }

    /**
     * @param CreateUserCommand $command
     * @throws UserCreationException
     * @return \stdClass
     */
    public function handle(CreateUserCommand $command): \stdClass
    {
        $user = $this->createUserEntity($command);

        return $this->presenter->toDto($this->orchestrator->createUser($user, $command->getCurrency()));
    }

    /**
     * @param CreateUserCommand $command
     * @return User
     */
    private function createUserEntity(CreateUserCommand $command): User
    {
        return (new User)
            ->setUsername($command->getUsername())
            ->setEmail($command->getEmail())
            ->setPasswordHash($command->getPassword());
    }
}
