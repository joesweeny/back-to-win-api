<?php

namespace BackToWin\Framework\Exception;

class UndefinedException extends \Exception
{
    public static function field(string $field): UndefinedException
    {
        return new self("Field '$field' is undefined");
    }
}
