<?php

namespace BackToWin\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180717204649 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('game');
        $table->addColumn('id', Type::BINARY)->setLength(16);
        $table->addColumn('type', Type::STRING);
        $table->addColumn('currency', Type::STRING);
        $table->addColumn('buy_in', Type::INTEGER);
        $table->addColumn('max', Type::INTEGER);
        $table->addColumn('min', Type::INTEGER);
        $table->addColumn('status', Type::STRING);
        $table->addColumn('start', Type::INTEGER);
        $table->addColumn('players', Type::INTEGER);
        $table->addColumn('created_at', Type::INTEGER);
        $table->addColumn('updated_at', Type::INTEGER);
        $table->setPrimaryKey(['id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('game');
    }
}
