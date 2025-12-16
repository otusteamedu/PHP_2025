<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250702042754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Table request';
    }

    public function up(Schema $schema): void
    {
        $table = $schema->createTable('request');
        $table->addColumn('id', 'integer', ['autoincrement' => true]);
        $table->addColumn('status', 'boolean', ['notnull' => false]);
        $table->setPrimaryKey(['id']);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
    }
}
