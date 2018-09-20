<?php

namespace GamePlatform\Application\Http\Api\v1\Validation\Avatar;

class RequestValidator
{
    /**
     * Validate Request body fields when adding an Avatar
     *
     * @param \stdClass $body
     * @return array
     */
    public function validate(\stdClass $body): array
    {
        $errors = [];

        if (!isset($body->user_id)) {
            $errors[] = "Required field 'user_id' is missing";
        }

        if (!isset($body->filename)) {
            $errors[] = "Required field 'filename' is missing";
        }

        if (!isset($body->contents)) {
            $errors[] = "Required field 'contents' is missing";
        }

        return $errors;
    }
}
