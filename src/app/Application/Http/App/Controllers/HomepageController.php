<?php

namespace BackToWin\Application\Http\App\Controllers;

use BackToWin\Framework\Buffer\OutputBuffer;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response\HtmlResponse;

class HomepageController
{
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return new HtmlResponse(OutputBuffer::capture(function () {
            require __DIR__ . '/../../App/Resources/home.php';
        }));
    }
}