<?php

use GamePlatform\Bootstrap\ContainerFactory;

require __DIR__ . '/vendor/autoload.php';

if (file_exists(__DIR__ . '/.env')) {
    (new \josegonzalez\Dotenv\Loader(__DIR__ . '/.env'))
        ->parse()
        ->putenv();
}

return (new ContainerFactory())->create(
    \GamePlatform\Bootstrap\ConfigFactory::create()
);