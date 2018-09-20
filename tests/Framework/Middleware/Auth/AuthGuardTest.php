<?php

namespace GamePlatform\Framework\Middleware\Auth;

use Chief\CommandBus;
use GamePlatform\Bootstrap\Config;
use GamePlatform\Boundary\Auth\Command\ValidateTokenCommand;
use GamePlatform\Framework\Exception\BadRequestException;
use GamePlatform\Framework\Exception\NotAuthenticatedException;
use GamePlatform\Framework\Exception\TokenExpiryException;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\TextResponse;

class AuthGuardTest extends TestCase
{
    /** @var  CommandBus */
    private $bus;
    /** @var  AuthGuard */
    private $guard;
    /** @var  RequestHandlerInterface */
    private $handler;
    /** @var  Config */
    private $config;

    public function setUp()
    {
        $this->bus = $this->prophesize(CommandBus::class);
        $this->config = $this->prophesize(Config::class);
        $this->guard = new AuthGuard($this->bus->reveal(), $this->config->reveal());
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
    }

    public function test_process_returns_response_if_request_is_correct_and_validated()
    {
        $request = new ServerRequest('GET', '/some/content', ['Authorization' => 'Bearer some-valid-token']);

        $this->config->get('auth.exempt-routes')->willReturn([]);

        $this->bus->execute(new ValidateTokenCommand('some-valid-token'))->shouldBeCalled();

        $this->handler->handle($request)->willReturn($mockResponse = new TextResponse('Hello Joe'));

        $response = $this->guard->process($request, $this->handler->reveal());

        $this->assertEquals($mockResponse, $response);
    }

    public function test_process_throws_exception_if_request_does_not_contain_authorization_header()
    {
        $request = new ServerRequest('GET', '/some/content');

        $this->config->get('auth.exempt-routes')->willReturn([]);

        $this->bus->execute(new ValidateTokenCommand(Argument::any()))->shouldNotBeCalled();

        $this->handler->handle($request)->shouldNotBeCalled();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Authorization header missing from Request');
        $this->guard->process($request, $this->handler->reveal());
    }

    public function test_process_throws_exception_if_authorization_token_header_value_is_in_an_incorrect_format()
    {
        $request = new ServerRequest('GET', '/some/content', ['Authorization' => 'some-valid-token']);

        $this->config->get('auth.exempt-routes')->willReturn([]);

        $this->bus->execute(new ValidateTokenCommand(Argument::any()))->shouldNotBeCalled();

        $this->handler->handle($request)->shouldNotBeCalled();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Authorization header value 'some-valid-token' is not in the correct format");
        $this->guard->process($request, $this->handler->reveal());
    }

    public function test_process_throws_exception_if_internal_token_validation_fails()
    {
        $request = new ServerRequest('GET', '/some/content', ['Authorization' => 'Bearer some-valid-token']);

        $this->config->get('auth.exempt-routes')->willReturn([]);

        $this->bus->execute(new ValidateTokenCommand('some-valid-token'))->willThrow(
            $e = new NotAuthenticatedException('Not authenticated')
        );

        $this->handler->handle($request)->shouldNotBeCalled();

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('Not authenticated');
        $this->guard->process($request, $this->handler->reveal());
    }

    public function test_process_can_throw_a_token_expiry_exception()
    {
        $request = new ServerRequest('GET', '/some/content', ['Authorization' => 'Bearer some-valid-token']);

        $this->config->get('auth.exempt-routes')->willReturn([]);

        $this->bus->execute(new ValidateTokenCommand('some-valid-token'))->willThrow(
            $e = new TokenExpiryException('Token expired')
        );

        $this->handler->handle($request)->shouldNotBeCalled();

        $this->expectException(TokenExpiryException::class);
        $this->expectExceptionMessage('Token expired');
        $this->guard->process($request, $this->handler->reveal());
    }

    public function test_validation_is_skipped_if_request_method_and_path_is_exempt_from_validation()
    {
        $request = new ServerRequest('GET', '/content');

        $this->config->get('auth.exempt-routes')->willReturn(['GET' => '/content']);

        $this->bus->execute(new ValidateTokenCommand('some-valid-token'))->shouldNotBeCalled();

        $this->handler->handle($request)->willReturn($mockResponse = new TextResponse('Hello Joe'));

        $response = $this->guard->process($request, $this->handler->reveal());

        $this->assertEquals($mockResponse, $response);
    }
}
