<?php

namespace BackToWin\Boundary\User\Command\Handlers;

use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Boundary\User\UserPresenter;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Boundary\User\Command\GetUserByIdCommand;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class GetUserByIdCommandHandlerTest extends TestCase
{
    /** @var  UserOrchestrator */
    private $orchestrator;
    /** @var  GetUserByIdCommandHandler */
    private $handler;
    /** @var  UserPresenter */
    private $presenter;

    public function setUp()
    {
        $this->orchestrator = $this->prophesize(UserOrchestrator::class);
        $this->presenter = $this->prophesize(UserPresenter::class);
        $this->handler = new GetUserByIdCommandHandler($this->orchestrator->reveal(), $this->presenter->reveal());
    }

    public function test_handle_returns_a_user_object_if_user_id_is_stored_in_the_database()
    {
        $command = new GetUserByIdCommand('dc5b6421-d452-4862-b741-d43383c3fe1d');

        $this->orchestrator->getUserById($command->getId())->shouldBeCalled()->willReturn(
            new User('dc5b6421-d452-4862-b741-d43383c3fe1d')
        );

        $this->presenter->toDto(Argument::type(User::class))->shouldBeCalled();

        $this->handler->handle($command);
    }

    public function test_handle_throws_not_found_exception_if_user_id_is_not_stored_in_database()
    {
        $command = new GetUserByIdCommand('dc5b6421-d452-4862-b741-d43383c3fe1d');

        $this->orchestrator->getUserById($command->getId())->shouldBeCalled()->willThrow(NotFoundException::class);

        $this->presenter->toDto(Argument::type(User::class))->shouldNotBeCalled();

        $this->expectException(NotFoundException::class);

        $this->handler->handle($command);
    }
}
