<?php

namespace GamePlatform\Framework\Calculation;

use MyCLabs\Enum\Enum;

/**
 * @method static Calculation ADD()
 * @method static Calculation SUBTRACT()
 */
class Calculation extends Enum
{
    const ADD = 'ADD';
    const SUBTRACT = 'SUBTRACT';
}
