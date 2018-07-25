<?php

namespace BackToWin\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180723202425 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('game_entry');
        $table->addColumn('id', Type::INTEGER)->setAutoincrement(true);
        $table->addColumn('game_id', Type::BINARY);
        $table->addColumn('user_id', Type::BINARY);
        $table->addColumn('timestamp', Type::INTEGER);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['game_id', 'user_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('game_entry');
    }
}
