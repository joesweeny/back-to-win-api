<?php

namespace BackToWin\Domain\Auth\Services\Token;

use Cake\Chronos\Chronos;
use BackToWin\Bootstrap\Config;
use BackToWin\Domain\User\Entity\User;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\DateTime\SystemClock;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

class TokenOrchestratorTest extends TestCase
{
    /** @var  TokenGenerator */
    private $generator;
    /** @var  Config */
    private $config;
    /** @var  Clock */
    private $clock;
    /** @var  UserOrchestrator */
    private $userOrchestrator;
    /** @var  LoggerInterface */
    private $logger;
    /** @var  TokenOrchestrator */
    private $tokenOrchestrator;

    public function setUp()
    {
        $this->generator = $this->prophesize(TokenGenerator::class);
        $this->config = $this->prophesize(Config::class);
        $this->clock = $this->prophesize(Clock::class);
        $this->userOrchestrator = $this->prophesize(UserOrchestrator::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->tokenOrchestrator = new TokenOrchestrator(
            $this->generator->reveal(),
            $this->config->reveal(),
            $this->clock->reveal(),
            $this->userOrchestrator->reveal(),
            $this->logger->reveal()
        );
    }

    public function test_token_string_is_generated_if_user_exists_with_uuid_provided()
    {
        $this->userOrchestrator->getUserById($id = new Uuid('d215d56b-4b20-437d-9773-be5ec8dd2c19'))->willReturn(
            $user = new User('d215d56b-4b20-437d-9773-be5ec8dd2c19')
        );

        $this->clock->now()->willReturn(new Chronos('2018-08-30 00:00:00'));

        $this->config->get('auth.token.expiry')->willReturn(1440);

        $this->generator->generate($user->getId(), new \DateTimeImmutable('2018-08-31 00:00:00'))->willReturn(
            'Some random access token string'
        );

        $this->tokenOrchestrator->createNewToken($id);

        $this->addToAssertionCount(1);
    }

    public function test_exception_is_logged_and_then_thrown_if_user_with_uuid_provided_does_not_exist()
    {
        $this->userOrchestrator->getUserById($id = new Uuid('d215d56b-4b20-437d-9773-be5ec8dd2c19'))->willThrow(
            $e = new NotFoundException('User not found with this ID')
        );

        $this->clock->now()->shouldNotBeCalled();

        $this->generator->generate(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->config->get('auth.token.expiry')->shouldNotBeCalled();
        
        $this->logger->error(
            'An illegal attempt has been made to generate an access Token with User ID d215d56b-4b20-437d-9773-be5ec8dd2c19'
        )->shouldBeCalled();

        $this->expectException(NotFoundException::class);
        $this->tokenOrchestrator->createNewToken($id);
    }
}
