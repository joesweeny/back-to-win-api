<?php

namespace GamePlatform\Framework\Middleware\Error;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Log\LoggerInterface;

class ErrorHandler implements MiddlewareInterface
{
    /**
     * @var ErrorResponseFactory
     */
    private $responseFactory;
    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(ErrorResponseFactory $responseFactory, LoggerInterface $logger)
    {
        $this->responseFactory = $responseFactory;
        $this->logger = $logger;
    }

    /**
     * @inheritdoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (\Throwable $e) {
            $this->logger->error(
                "Exception caught in ErrorHandler middleware: {$e->getMessage()}",
                [
                    'exception' => $e
                ]
            );
            
            return $this->responseFactory->create($e);
        }
    }
}
