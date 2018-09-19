<?php

namespace GamePlatform\Application\Console\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20180919143252 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        $table = $schema->createTable('avatar');
        $table->addColumn('user_id', Type::BINARY)->setLength(16);
        $table->addColumn('filename', Type::STRING);
        $table->addColumn('created_at', Type::INTEGER);
        $table->addColumn('updated_at', Type::INTEGER);
        $table->setPrimaryKey(['user_id']);
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        $schema->dropTable('avatar');
    }
}
