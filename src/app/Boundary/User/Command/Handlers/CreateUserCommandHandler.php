<?php

namespace BackToWin\Boundary\User\Command\Handlers;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Boundary\User\UserPresenter;
use BackToWin\Boundary\User\Command\CreateUserCommand;
use BackToWin\Framework\Exception\UserCreationException;

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

        if ($this->orchestrator->userExistsWithEmail($user)) {
            throw new UserCreationException("A user has already registered with this email address {$user->getEmail()}");
        }

        if ($this->orchestrator->userExistsWithUsername($user)) {
            throw new UserCreationException("A user has already registered with this username {$user->getUsername()}");
        }

        return $this->presenter->toDto($this->orchestrator->createUser($user));
    }

    /**
     * @param CreateUserCommand $command
     * @return User
     */
    private function createUserEntity(CreateUserCommand $command): User
    {
        return (new User)
            ->setUsername($command->getUsername())
            ->setFirstName($command->getFirstName())
            ->setLastName($command->getLastName())
            ->setLocation($command->getLocation())
            ->setEmail($command->getEmail())
            ->setPasswordHash($command->getPassword());
    }
}
