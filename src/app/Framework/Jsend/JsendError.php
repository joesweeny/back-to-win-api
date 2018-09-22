<?php

namespace BackToWin\Framework\Jsend;

class JsendError implements \JsonSerializable
{
    /**
     * @var string
     */
    private $message;
    /**
     * @var int
     */
    private $errorCode;

    /**
     * JsendError constructor.
     *
     * @param string $message
     *  See getMessage()
     *
     * @param int $errorCode
     *  See getErrorCode()
     */
    public function __construct(string $message, int $errorCode = 1)
    {
        $this->message = $message;
        $this->errorCode = $errorCode;
    }

    /**
     * @return int
     *  Get an error code for this error. Error codes can be distributed to your clients so that they
     *  can handle unique errors without having to attempt to do string matches on the error message.
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * @return string
     *  Get a client-friendly message describing the error.
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    public function jsonSerialize()
    {
        return (object) [
            'message' => $this->getMessage(),
            'code' => $this->getErrorCode()
        ];
    }
}
