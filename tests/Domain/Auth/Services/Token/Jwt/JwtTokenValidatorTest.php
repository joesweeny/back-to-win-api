<?php

namespace BackToWin\Domain\Auth\Services\Token\Jwt;

use BackToWin\Bootstrap\Config;
use BackToWin\Domain\Auth\Services\Token\TokenGenerator;
use BackToWin\Domain\Auth\Services\Token\TokenValidator;
use BackToWin\Framework\Exception\NotAuthenticatedException;
use BackToWin\Framework\Exception\TokenExpiryException;
use BackToWin\Framework\Uuid\Uuid;
use BackToWin\Testing\Traits\UsesContainer;
use Interop\Container\ContainerInterface;
use PHPUnit\Framework\TestCase;

class JwtTokenValidatorTest extends TestCase
{
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Config */
    private $config;
    /** @var  TokenValidator */
    private $validator;

    public function setUp()
    {
        $this->container = $this->createContainer();
        $this->config = $this->container->get(Config::class);
        $this->config->set('auth.token.driver', 'jwt');
        $this->validator = $this->container->get(TokenValidator::class);
    }

    public function test_validate_executes_with_exception_thrown_if_token_is_valid()
    {
        $token = $this->createToken((new \DateTimeImmutable())->add(new \DateInterval('P4D')));

        $this->validator->validate($token);

        $this->addToAssertionCount(1);
    }

    public function test_validate_throws_exception_when_validating_a_token_not_created_by_this_application()
    {
        $token = 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwia' .
            'WF0IjoxNTE2MjM5MDIyfQ.SflKxwRJSMeKKF2QT4fwpMeJf36POk6yJV_adQssw5c';

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('Token provided failed validation');
        $this->validator->validate($token);
    }

    public function test_validate_throws_exception_if_token_created_by_application_is_tampered_with()
    {
        $token = $this->createToken((new \DateTimeImmutable())->add(new \DateInterval('P4D')));

        $token .= 'ee';

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('Token provided failed validation');
        $this->validator->validate($token);
    }

    public function test_validate_throws_exception_if_token_has_expired()
    {
        $token = $this->createToken((new \DateTimeImmutable())->sub(new \DateInterval('P4D')));

        $this->expectException(TokenExpiryException::class);
        $this->expectExceptionMessage('Token provided has expired');
        $this->validator->validate($token);
    }

    private function createToken(\DateTimeImmutable $expiry)
    {
        $generator = $this->container->get(TokenGenerator::class);

        return $generator->generate(Uuid::generate(), $expiry);
    }
}
