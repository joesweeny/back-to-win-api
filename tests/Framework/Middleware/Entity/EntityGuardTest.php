<?php

namespace GamePlatform\Framework\Middleware\Entity;

use GamePlatform\Framework\Exception\BadRequestException;
use GamePlatform\Framework\Exception\NotAuthenticatedException;
use GuzzleHttp\Psr7\ServerRequest;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\TextResponse;

class EntityGuardTest extends TestCase
{
    /** @var  Parser */
    private $parser;
    /** @var  EntityGuard */
    private $guard;
    /** @var  RequestHandlerInterface */
    private $handler;

    public function setUp()
    {
        $this->parser = $this->prophesize(Parser::class);
        $this->guard = new EntityGuard($this->parser->reveal());
        $this->handler = $this->prophesize(RequestHandlerInterface::class);
    }

    public function test_process_returns_response_if_request_is_correct_and_is_allowed_to_update_entity()
    {
        $request = new ServerRequest(
            'PUT',
            '/avatar',
            ['Authorization' => 'Bearer some-valid-token'],
            json_encode((object) ['user_id' => '1234'])
        );

        $this->parser->parse('some-valid-token')->willReturn(
            (new Builder())
                ->set('user_id', '1234')
                ->getToken()
        );

        $this->handler->handle(Argument::type(ServerRequest::class))->willReturn(
            $mockResponse = new TextResponse('Hello Joe')
        );

        $response = $this->guard->process($request, $this->handler->reveal());

        $this->assertEquals($mockResponse, $response);
    }

    public function test_process_throws_exception_if_request_does_not_contain_authorization_header()
    {
        $request = new ServerRequest(
            'PUT',
            '/avatar',
            [],
            json_encode((object) ['user_id' => '1234'])
        );

        $this->parser->parse(Argument::type('string'))->shouldNotBeCalled();

        $this->handler->handle($request)->shouldNotBeCalled();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage('Authorization header missing from Request');
        $this->guard->process($request, $this->handler->reveal());
    }

    public function test_process_throws_exception_if_required_request_body_field_is_missing_for_specific_route()
    {
        $request = new ServerRequest(
            'PUT',
            '/avatar',
            ['Authorization' => 'Bearer some-valid-token']
        );

        $this->parser->parse(Argument::type('string'))->shouldNotBeCalled();

        $this->handler->handle($request)->shouldNotBeCalled();

        $this->expectException(BadRequestException::class);
        $this->expectExceptionMessage("Required field 'user_id' is missing");
        $this->guard->process($request, $this->handler->reveal());
    }

    public function test_process_throws_exception_if_request_fails_validation()
    {
        $request = new ServerRequest(
            'PUT',
            '/avatar',
            ['Authorization' => 'Bearer some-valid-token'],
            json_encode((object) ['user_id' => '1234'])
        );

        $this->parser->parse('some-valid-token')->willReturn(
            (new Builder())
                ->set('user_id', '5678')
                ->getToken()
        );

        $this->handler->handle($request)->shouldNotBeCalled();

        $this->expectException(NotAuthenticatedException::class);
        $this->expectExceptionMessage('You are not authenticated to update this resource');
        $this->guard->process($request, $this->handler->reveal());
    }
}
