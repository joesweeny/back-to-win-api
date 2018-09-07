<?php

namespace GamePlatform\Application\Http\Api\v1\Validation\User;

class RequestValidator
{
    public function validate(\stdClass $body): array
    {
        $errors = [];

        if (!isset($body->username)) {
            $errors[] = "Required field 'username' is missing";
        }

        if (!isset($body->email)) {
            $errors[] = "Required field 'email' is missing";
        }

        if (!isset($body->password)) {
            $errors[] = "Required field 'password' is missing";
        }

        if (!isset($body->currency)) {
            $errors[] = "Required field 'currency' is missing";
        }

        return $errors;
    }
}
