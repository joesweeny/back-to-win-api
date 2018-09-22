<?php

namespace BackToWin\Application\Http\Api\v1\Controllers\OpenApi;

use BackToWin\Framework\Buffer\OutputBuffer;
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

    /**
     * @return ResponseInterface
     * @throws \InvalidArgumentException
     */
    public function spec(): ResponseInterface
    {
        $file = file_get_contents(__DIR__ . '/../../OpenApi/open-api.json');

        if (!$file) {
            throw new \InvalidArgumentException('Unable to parse Open Api spec JSON file');
        }

        return new JsonResponse(json_decode($file));
    }
}
