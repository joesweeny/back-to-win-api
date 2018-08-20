<?php

namespace GamePlatform\Framework\DateTime;

use Cake\Chronos\Chronos;

interface Clock
{
    /**
     * @return Chronos
     */
    public function getCurrentDatetime();

    /**
     * Alias of getCurrentDatetime()
     *
     * @return Chronos
     */
    public function now();
}
