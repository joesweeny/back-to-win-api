<?php

namespace GamePlatform\Application\Http\Api\v1\Controllers\OpenApi;

use GamePlatform\Framework\Buffer\OutputBuffer;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class OpenApiController
{
    public function __invoke(): ResponseInterface
    {
        return new HtmlResponse(OutputBuffer::capture(function () {
            require __DIR__ . '/../../Resources/open-api.php';
        }));
    }

    public function spec(): ResponseInterface
    {
        $data = json_decode(file_get_contents(__DIR__ . '/../../OpenApi/open-api.json'));

        return new JsonResponse($data);
    }
}
