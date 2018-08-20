<?php

namespace GamePlatform\Testing\Traits;

use Interop\Container\ContainerInterface;
use GamePlatform\Bootstrap\Config;
use GamePlatform\Bootstrap\ConfigFactory;
use GamePlatform\Bootstrap\ContainerFactory;

trait UsesContainer
{
    protected function createContainer(Config $config = null): ContainerInterface
    {
        return (new ContainerFactory)->create($config ?: ConfigFactory::create());
    }
}
