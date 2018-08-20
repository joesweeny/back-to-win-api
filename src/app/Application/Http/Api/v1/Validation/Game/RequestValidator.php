<?php

namespace GamePlatform\Application\Http\Api\v1\Validation\Game;

class RequestValidator
{
    /**
     * Validate Request body fields when creating a Game
     *
     * @param \stdClass $body
     * @return array
     */
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

    /**
     * Validate Request body fields when settling a Game
     *
     * @param \stdClass $body
     * @return array
     */
    public function validateSettle(\stdClass $body): array
    {
        $errors = [];

        if (!isset($body->game_id)) {
            $errors[] = "Required field 'game_id' is missing";
        }

        if (!isset($body->user_id)) {
            $errors[] = "Required field 'winner_id' is missing";
        }

        if (!isset($body->currency)) {
            $errors[] = "Required field 'currency' is missing";
        }

        if (!isset($body->amount)) {
            $errors[] = "Required field 'amount' is missing";
        }

        return $errors;
    }
}
