<?php

namespace BackToWin\Bootstrap;

use Chief\Busses\SynchronousCommandBus;
use Chief\CommandBus;
use Chief\Container;
use Chief\Resolvers\NativeCommandHandlerResolver;
use BackToWin\Application\Http\App\Routes\RouteManager;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\DateTime\SystemClock;
use Dflydev\FigCookies\SetCookie;
use DI\ContainerBuilder;
use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use Interop\Container\ContainerInterface;
use Lcobucci\JWT\Parser;
use BackToWin\Framework\CommandBus\ChiefAdapter;
use BackToWin\Framework\Routing\Router;
use PSR7Session\Http\SessionMiddleware;
use PSR7Session\Time\SystemCurrentTime;

class ContainerFactory
{
    /** @var Config */
    private $config;

    public function create(Config $config = null): ContainerInterface
    {
        $this->config = $config;

        return (new ContainerBuilder)
            ->useAutowiring(true)
            ->ignorePhpDocErrors(true)
            ->useAnnotations(false)
            ->writeProxiesToFile(false)
            ->addDefinitions($this->getDefinitions())
            ->build();
    }

    /**
     * @return array
     * @throws \UnexpectedValueException
     */
    protected function getDefinitions(): array
    {
        return array_merge(
            $this->defineConfig(),
            $this->defineFramework(),
            $this->defineDomain(),
            $this->defineConnections()
        );
    }

    protected function defineConfig(): array
    {
        return [
            Config::class => \DI\factory(function () {
                return $this->config;
            }),
        ];
    }

    /**
     * @return array
     * @throws \UnexpectedValueException
     */
    private function defineFramework(): array
    {
        return [
            ContainerInterface::class => \DI\factory(function (ContainerInterface $container) {
                return $container;
            }),


            Router::class => \DI\decorate(function (Router $router, ContainerInterface $container) {
                // @todo Add RouteManagers here
                return $router
                    ->addRoutes($container->get(RouteManager::class));


            }),

            CommandBus::class => \DI\factory(function (ContainerInterface $container) {
                $bus = new ChiefAdapter(new SynchronousCommandBus(new NativeCommandHandlerResolver(new class($container) implements Container {
                    /**
                     * @var ContainerInterface
                     */
                    private $container;
                    public function __construct(ContainerInterface $container)
                    {
                        $this->container = $container;
                    }
                    public function make($class)
                    {
                        return $this->container->get($class);
                    }
                })));
//                $bus->pushDecorator($container->get(AuthDecorator::class));
                return $bus;
            }),

            SessionMiddleware::class => \DI\factory(function (ContainerInterface $container) {
                return new SessionMiddleware(
                    new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                    'OpcMuKmoxVhzW0Y1iESpjWwL/D3UBdDauJOe742BJ5Q=',
                    'OpcMuKmoxVhzW0Y1iESpjWwL/D3UBdDauJOe742BJ5Q=',
                    SetCookie::create(SessionMiddleware::DEFAULT_COOKIE)
                        ->withSecure(false)
                        ->withHttpOnly(true)
                        ->withPath('/'),
                    new Parser(),
                    1200,
                    new SystemCurrentTime()
                );
            }),


            Clock::class => \DI\object(SystemClock::class)
        ];
    }

    /**
     * @return array
     */
    private function defineDomain(): array
    {
        return [

        ];
    }


    private function defineConnections()
    {
        return [
            Connection::class => \DI\factory(function (ContainerInterface $container) {

                $config = $container->get(Config::class);

                $dsn = $config->get('database.default.pdo.dsn');

                if (substr($dsn, 0, 5) === 'mysql') {
                    return new MySqlConnection($container->get(\PDO::class));
                }

                if (substr($dsn, 0, 6) === 'sqlite') {
                    return new SQLiteConnection($container->get(\PDO::class));
                }

                throw new \RuntimeException("Unrecognised DNS {$dsn}");
            }),

            \Doctrine\DBAL\Driver\Connection::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(Connection::class)->getDoctrineConnection();
            }),

            \PDO::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);
                $pdo = new \PDO(
                    $config->get('database.default.pdo.dsn'),
                    $config->get('database.default.pdo.user'),
                    $config->get('database.default.pdo.password')
                );
                $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                return $pdo;
            }),
        ];
    }

}
