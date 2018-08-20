<?php

namespace GamePlatform\Testing\Traits;

use GamePlatform\Bootstrap\Config;
use Interop\Container\ContainerInterface;
use Predis\Client;

trait UsesTestRedisDatabase
{
    protected function setRedisDatabase(ContainerInterface $container): ContainerInterface
    {
        $container->get(Config::class)->set('redis.default.database', 15);

        $container->get(Client::class)->flushdb();

        return $container;
    }

    protected function flushRedisDatabase(ContainerInterface $container)
    {
        $container->get(Client::class)->flushdb();
    }
}
