<?php

namespace BackToWin\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180815142706 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('admin_bank_transaction');
        $table->addColumn('id', Type::INTEGER)->setAutoincrement(true);
        $table->addColumn('game_id', Type::BINARY);
        $table->addColumn('currency', Type::STRING);
        $table->addColumn('amount', Type::INTEGER);
        $table->addColumn('timestamp', Type::INTEGER);
        $table->addIndex(['game_id', 'currency']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('admin_bank_transaction');
    }
}
