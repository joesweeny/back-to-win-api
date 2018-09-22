<?php

namespace BackToWin\Framework\Jsend;

use Zend\Diactoros\Response\JsonResponse;

/**
 * Class JsendResponse
 * @package Opia\Lib\Jsend
 */
class JsendResponse extends JsonResponse
{
    /**
     * @var string
     */
    private $jsendStatus;
    /**
     * @var object
     */
    private $jsendData;

    /**
     * JsendResponse constructor.
     * @param mixed $data
     * @param string $status
     * @param array $headers
     * @param int $encodingOptions
     * @internal You should use new JsendFailResponse, JsendErrorResponse, or JsendSuccessResponse instead
     * @throws \InvalidArgumentException
     */
    public function __construct($data, string $status = 'success', array $headers = [], int $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        $this->jsendStatus = $status;
        $this->jsendData = $data;

        $data = (object) [
            'status' => $status,
            'data' => $data
        ];

        switch ($status) {
            case 'success':
                $statusCode = 200;
                break;
            case 'fail':
                $statusCode = 400;
                break;
            case 'error':
                $statusCode = 500;
                break;
            default:
                throw new \InvalidArgumentException("Status '$status' is not a valid Jsend status");
        }

        parent::__construct($data, $statusCode, $headers, $encodingOptions);
    }

    /**
     * @param array $data
     * @param array $headers
     * @param int $encodingOptions
     * @return JsendResponse
     * @throws \InvalidArgumentException
     * @internal
     */
    public static function success($data = [], array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'success', $headers, $encodingOptions);
    }

    /**
     * @param mixed $data
     * @param array $headers
     * @param int $encodingOptions
     * @return JsendResponse
     * @throws \InvalidArgumentException
     * @internal
     */
    public static function fail($data, array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'fail', $headers, $encodingOptions);
    }

    /**
     * @param mixed $data
     * @param array $headers
     * @param int $encodingOptions
     * @return JsendResponse
     * @throws \InvalidArgumentException
     * @internal
     */
    public static function error($data, array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS): JsendResponse
    {
        return new static($data, 'error', $headers, $encodingOptions);
    }

    /**
     * @param string $json
     * @return JsendResponse
     * @throws \InvalidArgumentException
     */
    public static function fromSting(string $json): JsendResponse
    {
        $decoded = json_decode($json);
        if ($decoded === null) {
            throw new \InvalidArgumentException("JSON string could not be decoded '{$json}'");
        }

        return new JsendResponse($decoded->data, $decoded->status);
    }

    public function getJsendStatus(): string
    {
        return $this->jsendStatus;
    }

    public function isError(): bool
    {
        return $this->getJsendStatus() === 'error';
    }

    public function isFail(): bool
    {
        return $this->getJsendStatus() === 'fail';
    }

    public function isSuccess(): bool
    {
        return $this->getJsendStatus() === 'success';
    }

    /**
     * @return mixed
     */
    public function getJsendData()
    {
        return $this->jsendData;
    }
}
