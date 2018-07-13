<?php

namespace BackToWin\Testing\Traits;

use Interop\Container\ContainerInterface;
use BackToWin\Bootstrap\Config;
use BackToWin\Bootstrap\ConfigFactory;
use BackToWin\Bootstrap\ContainerFactory;

trait UsesContainer
{
    protected function createContainer(Config $config = null): ContainerInterface
    {
        return (new ContainerFactory)->create($config ?: ConfigFactory::create());
    }
}
