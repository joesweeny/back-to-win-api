<?php

namespace BackToWin\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180817145848 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('game_result');
        $table->addColumn('game_id', Type::BINARY)->setLength(16);
        $table->addColumn('winner_id', Type::BINARY)->setLength(16);
        $table->addColumn('timestamp', Type::INTEGER);
        $table->setPrimaryKey(['game_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('game_result');
    }
}
