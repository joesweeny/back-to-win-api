<?php

namespace BackToWin\Boundary\User\Command\Handlers;

use BackToWin\Boundary\User\Command\CreateUserCommand;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Boundary\User\UserPresenter;
use BackToWin\Framework\Exception\UserCreationException;
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
            'Joe',
            'Sweeny',
            'joe@email.com',
            'password',
            'Durham'
        );

        $orchestrator->userExistsWithEmail(Argument::type(User::class))->shouldBeCalled()->willReturn(false);
        $orchestrator->userExistsWithUsername(Argument::type(User::class))->shouldBeCalled()->willReturn(false);

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
            'Joe',
            'Sweeny',
            'joe@email.com',
            'password',
            'Durham'
        );

        $orchestrator->userExistsWithEmail(Argument::type(User::class))->shouldBeCalled()->willReturn(true);

        $orchestrator->createUser(Argument::type(User::class))->shouldNotBeCalled();

        $this->expectException(UserCreationException::class);
        $this->expectExceptionMessage('A user has already registered with this email address joe@email.com');

        $handler->handle($command);
    }
}
