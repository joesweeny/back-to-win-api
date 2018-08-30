<?php

namespace GamePlatform\Domain\Auth\Services\Token\Jwt;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Domain\Auth\Services\Token\TokenGenerator;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Uuid\Uuid;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;

class JwtTokenGenerator implements TokenGenerator
{
    const USER_ID = 'user_id';
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Clock
     */
    private $clock;
    /**
     * @var Sha256
     */
    private $signer;

    public function __construct(Config $config, Clock $clock, Sha256 $signer)
    {
        $this->config = $config;
        $this->clock = $clock;
        $this->signer = $signer;
    }

    /**
     * @inheritdoc
     */
    public function generate(Uuid $userId, \DateTimeImmutable $expiry): string
    {
        $token = (new Builder())
            ->setIssuedAt($this->clock->now()->getTimestamp())
            ->setExpiration($expiry->getTimestamp())
            ->set(self::USER_ID, (string) $userId)
            ->sign($this->signer, $this->config->get('auth.jwt.secret'))
            ->getToken();

        return $token->__toString();
    }
}
