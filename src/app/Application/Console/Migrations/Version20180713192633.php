<?php

namespace BackToWin\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180713192633 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('user');
        $table->addColumn('id', Type::BINARY)->setLength(16);
        $table->addColumn('username', Type::STRING);
        $table->addColumn('email', Type::STRING);
        $table->addColumn('password', Type::STRING);
        $table->addColumn('created_at', Type::DATETIME);
        $table->addColumn('updated_at', Type::DATETIME);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('user');
    }
}
