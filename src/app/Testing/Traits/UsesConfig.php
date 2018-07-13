<?php

namespace BackToWin\Testing\Traits;

use BackToWin\Bootstrap\Config;
use BackToWin\Bootstrap\ConfigFactory;

trait UsesConfig
{
    protected function createConfig(): Config
    {
        return ConfigFactory::create();
    }
}
