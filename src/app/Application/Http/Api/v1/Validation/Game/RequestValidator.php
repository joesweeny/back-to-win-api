<?php

namespace GamePlatform\Application\Http\Api\v1\Validation\Game;

class RequestValidator
{
    public function validateCreate(\stdClass $body): array
    {
        $errors = [];

        if (!isset($body->type)) {
            $errors[] = "Required field 'type' is missing";
        }

        if (!isset($body->currency)) {
            $errors[] = "Required field 'currency' is missing";
        }

        if (!isset($body->max)) {
            $errors[] = "Required field 'buy_in' is missing";
        }

        if (!isset($body->max)) {
            $errors[] = "Required field 'max' is missing";
        }

        if (!isset($body->min)) {
            $errors[] = "Required field 'min' is missing";
        }

        if (!isset($body->start)) {
            $errors[] = "Required field 'start' is missing";
        }

        if (!isset($body->players)) {
            $errors[] = "Required field 'players' is missing";
        }

        return $errors;
    }
}
