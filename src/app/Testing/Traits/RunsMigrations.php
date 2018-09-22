<?php

namespace BackToWin\Testing\Traits;

use Doctrine\DBAL\Schema\AbstractSchemaManager;
use Interop\Container\ContainerInterface;
use BackToWin\Application\Console\Console;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

trait RunsMigrations
{
    protected function runMigrations(ContainerInterface $container): ContainerInterface
    {
        // Drop the DB
        $db = $container->get(AbstractSchemaManager::class);
        foreach ($db->listTableNames() as $table) {
            $db->dropTable($table);
        }

        $console = $container->get(Console::class);
        $console->run(new StringInput('migrations:migrate --no-interaction --quiet'), $output = new BufferedOutput);

        $output = $output->fetch();

        if ($output) {
            $this->fail("Migrations output something that wasn't expected: \n\n $output");
        }

        return $container;
    }
}
