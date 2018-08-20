<?php

namespace GamePlatform\Framework\Jsend;

class JsendSuccessResponse extends JsendResponse
{
    /**
     * JsendSuccessResponse constructor.
     * @param mixed $data
     * @param array $headers
     * @param int $encodingOptions
     * @throws \InvalidArgumentException
     */
    public function __construct($data = null, array $headers = [], int $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        parent::__construct($data, 'success', $headers, $encodingOptions);
    }
}
