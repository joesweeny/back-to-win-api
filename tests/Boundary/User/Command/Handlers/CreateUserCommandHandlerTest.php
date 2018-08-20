<?php

namespace GamePlatform\Boundary\User\Command\Handlers;

use GamePlatform\Boundary\User\Command\CreateUserCommand;
use GamePlatform\Domain\User\Entity\User;
use GamePlatform\Domain\User\UserOrchestrator;
use GamePlatform\Boundary\User\UserPresenter;
use GamePlatform\Framework\Exception\UserCreationException;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class CreateUserCommandHandlerTest extends TestCase
{
    public function test_handle_create_a_new_user_record_in_the_database()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        /** @var UserPresenter $presenter */
        $presenter = $this->prophesize(UserPresenter::class);
        $handler = new CreateUserCommandHandler($orchestrator->reveal(), $presenter->reveal());

        $command = new CreateUserCommand(
            'joesweeny',
            'joe@email.com',
            'password'
        );

        $orchestrator->createUser(Argument::that(function (User $user) {
            $this->assertEquals('joe@email.com', $user->getEmail());
            return true;
        }))->shouldBeCalled();

        $presenter->toDto(Argument::type(User::class))->shouldBeCalled();

        $handler->handle($command);
    }

    public function test_handle_does_not_create_a_new_user_record_in_the_database_if_user_with_email_already_exists()
    {
        /** @var UserOrchestrator $orchestrator */
        $orchestrator = $this->prophesize(UserOrchestrator::class);
        /** @var UserPresenter $presenter */
        $presenter = $this->prophesize(UserPresenter::class);
        $handler = new CreateUserCommandHandler($orchestrator->reveal(), $presenter->reveal());

        $command = new CreateUserCommand(
            'joesweeny',
            'joe@email.com',
            'password'
        );

        $orchestrator->createUser(Argument::type(User::class))->willThrow(
            new UserCreationException('A user has already registered with this email address joe@email.com')
        );

        $this->expectException(UserCreationException::class);
        $this->expectExceptionMessage('A user has already registered with this email address joe@email.com');

        $handler->handle($command);
    }
}
