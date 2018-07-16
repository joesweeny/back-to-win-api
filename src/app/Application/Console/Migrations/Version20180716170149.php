<?php

namespace BackToWin\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180716170149 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->getTable('user');
        $table->setPrimaryKey(['id']);

        $table = $schema->createTable('user_purse');
        $table->addColumn('user_id', Type::BINARY)->setLength(16);
        $table->addColumn('currency', Type::STRING)->setDefault('GBP');
        $table->addColumn('amount', Type::INTEGER)->setDefault(0);
        $table->addColumn('created_at', Type::INTEGER);
        $table->addColumn('updated_at', Type::INTEGER);
        $table->setPrimaryKey(['user_id']);

        $table = $schema->createTable('user_purse_transaction');
        $table->addColumn('id', Type::BINARY)->setLength(16);
        $table->addColumn('user_id', Type::BINARY)->setLength(16);
        $table->addColumn('currency', Type::STRING);
        $table->addColumn('amount', Type::INTEGER);
        $table->addColumn('calculation', Type::STRING);
        $table->addColumn('description', Type::STRING)->setNotnull(false);
        $table->addColumn('timestamp', Type::INTEGER);
        $table->setPrimaryKey(['id']);
        $table->addIndex(['user_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $table = $schema->getTable('user');
        $table->dropPrimaryKey();

        $schema->dropTable('user_purse')
            ->dropTable('user_purse_transaction');
    }
}
