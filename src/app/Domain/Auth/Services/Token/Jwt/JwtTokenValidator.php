<?php

namespace GamePlatform\Domain\Auth\Services\Token\Jwt;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Domain\Auth\Services\Token\TokenValidator;
use GamePlatform\Framework\DateTime\Clock;
use GamePlatform\Framework\Exception\NotAuthenticatedException;
use GamePlatform\Framework\Exception\TokenExpiryException;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Token;

class JwtTokenValidator implements TokenValidator
{
    /**
     * @var Parser
     */
    private $parser;
    /**
     * @var Sha256
     */
    private $signer;
    /**
     * @var Config
     */
    private $config;
    /**
     * @var Clock
     */
    private $clock;

    public function __construct(Parser $parser, Sha256 $signer, Config $config, Clock $clock)
    {
        $this->parser = $parser;
        $this->signer = $signer;
        $this->config = $config;
        $this->clock = $clock;
    }

    /**
     * @inheritdoc
     */
    public function validate(string $token): void
    {
        $parsed = $this->parser->parse($token);

        $this->verifyToken($parsed);

        $this->checkExpiryDate($parsed);
    }

    /**
     * @param Token $token
     * @throws NotAuthenticatedException
     */
    private function verifyToken(Token $token): void
    {
        if (!$token->verify($this->signer, $this->config->get('auth.jwt.secret'))) {
            throw new NotAuthenticatedException('Token provided failed validation');
        }
    }

    /**
     * @param Token $token
     * @throws TokenExpiryException
     */
    private function checkExpiryDate(Token $token): void
    {
        $expiry = \DateTimeImmutable::createFromFormat('U', $token->getClaim('exp'));

        if (!$expiry || $expiry < $this->clock->now()) {
            throw new TokenExpiryException('Token provided has expired');
        }
    }
}
