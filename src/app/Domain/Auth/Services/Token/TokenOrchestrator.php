<?php

namespace BackToWin\Domain\Auth\Services\Token;

use BackToWin\Bootstrap\Config;
use BackToWin\Domain\User\UserOrchestrator;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\Exception\NotFoundException;
use BackToWin\Framework\Uuid\Uuid;
use Psr\Log\LoggerInterface;

class TokenOrchestrator
{
    /**
     * @var TokenGenerator
     */
    private $generator;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Clock
     */
    private $clock;
    /**
     * @var UserOrchestrator
     */
    private $userOrchestrator;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        TokenGenerator $generator,
        Config $config,
        Clock $clock,
        UserOrchestrator $userOrchestrator,
        LoggerInterface $logger
    ) {
        $this->generator = $generator;
        $this->config = $config;
        $this->clock = $clock;
        $this->userOrchestrator = $userOrchestrator;
        $this->logger = $logger;
    }

    /**
     * Create a new application access token for a User
     *
     * @param Uuid $userId
     * @return string
     * @throws NotFoundException
     */
    public function createNewToken(Uuid $userId): string
    {
        try {
            $user = $this->userOrchestrator->getUserById($userId);
        } catch (NotFoundException $e) {
            $this->logger->error("An illegal attempt has been made to generate an access Token with User ID {$userId}");
            throw $e;
        }

        $expiry = $this->clock->now()->addMinutes($this->config->get('auth.token.expiry'));

        return $this->generator->generate($user->getId(), $expiry);
    }
}
