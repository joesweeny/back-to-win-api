<?php

namespace BackToWin\Bootstrap;

use Interop\Container\ContainerInterface;

class ConfigFactory
{
    /**
     * @return Config
     */
    public static function create(): Config
    {
        return new Config([
            'database' => [
                'default' => [
                    'pdo' => [
                        'dsn' => getenv('DB_DSN') ?: 'sqlite::memory:',
                        'user' => getenv('DB_USER') ?: 'username',
                        'password' => getenv('DB_PASSWORD') ?: 'password'
                    ]
                ]
            ],

            'log' => [
                /**
                 * Which psr/log implementation to use. Options: monolog, null
                 */
                'logger' => self::fromEnv('LOG_LOGGER') ?: 'null'
            ],

            'redis' => [
                'default' => [
                    'host'     => self::fromEnv('REDIS_HOST') ?: 'redis',
                    'port'     => 6379,
                    'database' => self::fromEnv('REDIS_DATABASE') ?: 0
                ],
            ],

            'bank' => [
                'driver' => self::fromEnv('BANK_DRIVER'),

                'entry-fee' => [
                    'store-driver' => self::fromEnv('ENTRY_FEE_STORE_DRIVER')
                ]
            ],

            'admin' => [
                'bank' => [
                    'driver' => self::fromEnv('ADMIN_BANK_DRIVER')
                ]
            ]
        ]);
    }

    /**
     * Get an value from ENV.
     *
     * If the value does not exist in ENV, then search for a {key}_FILE ENV variable.. if the _FILE
     * var exists, then read the contents of that file to get the value.
     *
     * @param string $key
     * @param string|null $default
     * @return mixed|null
     */
    private static function fromEnv(string $key, string $default = null)
    {
        if ($val = getenv($key)) {
            return $val;
        }

        if ($path = getenv("{$key}_FILE")) {
            if (file_exists($path)) {
                return file_get_contents($path);
            }
        }

        return $default;
    }

    /**
     * @param ContainerInterface $container
     * @return Config
     */
    public static function fromContainer(ContainerInterface $container): Config
    {
        return $container->get(Config::class);
    }
}
