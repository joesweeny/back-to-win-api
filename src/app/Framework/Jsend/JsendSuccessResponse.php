<?php

namespace BackToWin\Framework\Jsend;

class JsendSuccessResponse extends JsendResponse
{
    /**
     * JsendSuccessResponse constructor.
     * @param mixed $data
     * @param array $headers
     * @param array|int $encodingOptions
     * @throws \InvalidArgumentException
     */
    public function __construct($data = null, array $headers = [], $encodingOptions = self::DEFAULT_JSON_FLAGS)
    {
        parent::__construct($data, 'success', $headers, $encodingOptions);
    }
}
