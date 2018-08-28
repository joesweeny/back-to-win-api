<?php

namespace GamePlatform\Domain\Auth\Services\Token\Jwt;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Domain\Auth\Services\Token\Generator;
use GamePlatform\Framework\Uuid\Uuid;
use GamePlatform\Testing\Traits\UsesContainer;
use Interop\Container\ContainerInterface;
use Lcobucci\JWT\Parser;
use PHPUnit\Framework\TestCase;

class JwtGeneratorTest extends TestCase
{
    use UsesContainer;

    /** @var  ContainerInterface */
    private $container;
    /** @var  Generator */
    private $generator;
    /** @var  Config */
    private $config;

    public function setUp()
    {
        $this->container = $this->createContainer();
        $this->config = $this->container->get(Config::class);
        $this->config->set('auth.token.driver', 'jwt');
        $this->generator = $this->container->get(Generator::class);
    }

    public function test_interface_is_bound()
    {
        $this->assertInstanceOf(Generator::class, $this->generator);
    }

    public function test_generate_returns_a_valid_encoded_jwt_token_string()
    {
        $token = $this->generator->generate(
            new Uuid('058b1dc8-d168-4bab-aa4a-ffeed90c5435'),
            (new \DateTimeImmutable())->add(new \DateInterval('P1D'))
        );

        $parsed = (new Parser())->parse($token);

        $this->assertEquals('058b1dc8-d168-4bab-aa4a-ffeed90c5435', $parsed->getClaim('user_id'));
        $this->assertFalse($parsed->isExpired());
        $this->assertEquals('JWT', $parsed->getHeader('typ'));
        $this->assertEquals('HS256', $parsed->getHeader('alg'));
    }
}
