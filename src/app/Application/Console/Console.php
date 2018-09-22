<?php

namespace BackToWin\Application\Console;

use BackToWin\Application\Console\Command\AdminBankBalanceCommand;
use BackToWin\Application\Console\Command\GameCreateCommand;
use Doctrine\DBAL\Driver\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Finder\GlobFinder;
use Doctrine\DBAL\Migrations\OutputWriter;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use BackToWin\Application\Console\Command\UserCreateCommand;
use BackToWin\Application\Console\Command\UserFundsAddCommand;
use Interop\Container\ContainerInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Console
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Console constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        $app = new Application('CockTales Command Line');

        $app->setAutoExit(false);

        $configHelper = new ConfigurationHelper($this->container->get(Connection::class), $doctrineConfig = new Configuration(
            $this->container->get(Connection::class),
            null,
            new GlobFinder
        ));

        $doctrineConfig->setMigrationsNamespace(__NAMESPACE__ . '\\Migrations');
        $doctrineConfig->setMigrationsDirectory(__DIR__ . '/Migrations');
        $doctrineConfig->setOutputWriter(new OutputWriter(function (string $message) use ($output) {
            $output->writeln($message);
        }));

        $app->setHelperSet(new HelperSet([
            'dialog' => new QuestionHelper(),
            'configuration' => $configHelper
        ]));

        $app->addCommands([
            $this->container->get(AdminBankBalanceCommand::class),
            $this->container->get(GameCreateCommand::class),
            $this->container->get(UserCreateCommand::class),
            $this->container->get(UserFundsAddCommand::class),

            new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand()
        ]);

        return $app->run($input, $output);
    }
}
