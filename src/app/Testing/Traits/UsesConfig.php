<?php

namespace GamePlatform\Testing\Traits;

use GamePlatform\Bootstrap\Config;
use GamePlatform\Bootstrap\ConfigFactory;

trait UsesConfig
{
    protected function createConfig(): Config
    {
        return ConfigFactory::create();
    }
}
