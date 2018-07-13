<?php

namespace BackToWin\Framework\Controller;

use Zend\Diactoros\Response\JsonResponse;

class JsendResponse extends JsonResponse
{
    public function __construct($data, string $status = 'success', array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        $data = (object) [
            'status' => $status,
            'data' => $data
        ];

        switch ($status) {
            case 'success':
                $statusCode = 200;
                break;
            case 'fail':
                $statusCode = 401;
                break;
            case 'error':
                $statusCode = 500;
                break;
            default:
                throw new \InvalidArgumentException("Status '$status' is not a valid Jsend status");
        }

        parent::__construct($data, $statusCode, $headers, $encodingOptions);
    }

    public static function success($data = [], array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'success', $headers, $encodingOptions);
    }

    public static function fail($data, array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'fail', $headers, $encodingOptions);
    }

    public static function error($data, array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'error', $headers, $encodingOptions);
    }
}