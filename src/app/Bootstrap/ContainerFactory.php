<?php

namespace BackToWin\Bootstrap;

use BackToWin\Domain\Bank\Bank;
use BackToWin\Domain\Bank\User\RedisBank;
use BackToWin\Domain\GameEntry\Services\EntryFeeStore;
use BackToWin\Domain\GameEntry\Services\RedisEntryFeeStore;
use Chief\Busses\SynchronousCommandBus;
use Chief\CommandBus;
use Chief\Container;
use Chief\Resolvers\NativeCommandHandlerResolver;
use BackToWin\Framework\DateTime\Clock;
use BackToWin\Framework\DateTime\SystemClock;
use Dflydev\FigCookies\SetCookie;
use DI\ContainerBuilder;
use function DI\object;
use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Illuminate\Database\Connection;
use Illuminate\Database\MySqlConnection;
use Illuminate\Database\SQLiteConnection;
use Interop\Container\ContainerInterface;
use Lcobucci\JWT\Parser;
use BackToWin\Framework\CommandBus\ChiefAdapter;
use BackToWin\Framework\Routing\Router;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Predis\Client;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use PSR7Session\Http\SessionMiddleware;
use PSR7Session\Time\SystemCurrentTime;

class ContainerFactory
{
    /** @var Config|null */
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
                return $router
                    ->addRoutes($container->get(\BackToWin\Application\Http\App\Routes\RouteManager::class))
                    ->addRoutes($container->get(\BackToWin\Application\Http\Api\v1\Routing\User\RouteManager::class))
                    ->addRoutes($container->get(\BackToWin\Application\Http\Api\v1\Routing\UserPurse\RouteManager::class))
                    ->addRoutes($container->get(\BackToWin\Application\Http\Api\v1\Routing\Game\RouteManager::class));
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

            LoggerInterface::class => \DI\factory(function (ContainerInterface $container) {
                switch ($logger = $container->get(Config::class)->get('log.logger')) {
                    case 'monolog':
                        $logger = new Logger('error');
                        $logger->pushHandler(new ErrorLogHandler);
                        $logger->pushHandler(new StreamHandler(__DIR__ . '/../../logs/error.log', Logger::ERROR));
                        return $logger;

                    case 'null':
                        return new NullLogger;

                    default:
                        throw new \UnexpectedValueException("Logger '$logger' not recognised");
                }
            }),

            Clock::class => \DI\object(SystemClock::class),

            Client::class => \DI\factory(function (ContainerInterface $container) {
                $config = $container->get(Config::class);

                return new Client($config->get('redis.default'));
            }),

            Bank::class => \DI\factory(function (ContainerInterface $container) {
                switch ($bank = $container->get(Config::class)->get('bank.bank-driver')) {
                    case 'redis':
                        return new RedisBank($container->get(Client::class));
                    default:
                        throw new \UnexpectedValueException("Bank '$bank' not recognised");
                }
            }),

            \BackToWin\Domain\Admin\Bank\Bank::class => \DI\factory(function (ContainerInterface $container) {
                switch ($bank = $container->get(Config::class)->get('admin.bank.driver')) {
                    case 'redis':
                        return new \BackToWin\Domain\Admin\Bank\Redis\RedisBank($container->get(Client::class));
                    default:
                        throw new \UnexpectedValueException("Admin bank '$bank' not recognised");
                }
            }),

            EntryFeeStore::class => \DI\factory(function (ContainerInterface $container) {
                switch ($store = $container->get(Config::class)->get('bank.entry-fee.store-driver')) {
                    case 'redis':
                        return new RedisEntryFeeStore($container->get(Client::class));
                    default:
                        throw new \UnexpectedValueException("Entry fee store '$store' not recognised");
                }
            })
        ];
    }

    /**
     * @return array
     */
    private function defineDomain(): array
    {
        return [
            \BackToWin\Domain\User\Persistence\Reader::class => \DI\object(\BackToWin\Domain\User\Persistence\Illuminate\IlluminateReader::class),

            \BackToWin\Domain\User\Persistence\Writer::class => \DI\object(\BackToWin\Domain\User\Persistence\Illuminate\IlluminateWriter::class),

            \BackToWin\Domain\UserPurse\Persistence\Writer::class => \DI\object(\BackToWin\Domain\UserPurse\Persistence\Illuminate\IlluminateWriter::class),

            \BackToWin\Domain\UserPurse\Persistence\Reader::class => \DI\object(\BackToWin\Domain\UserPurse\Persistence\Illuminate\IlluminateReader::class),

            \BackToWin\Domain\Game\Persistence\Writer::class => \DI\object(\BackToWin\Domain\Game\Persistence\Illuminate\IlluminateWriter::class),

            \BackToWin\Domain\Game\Persistence\Reader::class => \DI\object(\BackToWin\Domain\Game\Persistence\Illuminate\IlluminateReader::class),

            \BackToWin\Domain\GameEntry\Persistence\Repository::class => \DI\object(\BackToWin\Domain\GameEntry\Persistence\Illuminate\IlluminateRepository::class),

            \BackToWin\Domain\Admin\Bank\Persistence\Repository::class => \DI\object(\BackToWin\Domain\Admin\Bank\Persistence\Illuminate\IlluminateRepository::class),

            \BackToWin\Domain\GameResult\Persistence\Repository::class => \DI\object(\BackToWin\Domain\GameResult\Persistence\Illuminate\IlluminateRepository::class),
        ];
    }


    private function defineConnections()
    {
        return [
            AbstractSchemaManager::class => \DI\factory(function (ContainerInterface $container) {
                return $container->get(Connection::class)->getDoctrineSchemaManager();
            }),

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
