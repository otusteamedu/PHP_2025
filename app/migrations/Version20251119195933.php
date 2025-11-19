<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251119195933 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы задач';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE "task" (email VARCHAR(255) NOT NULL, title VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITH TIME ZONE NOT NULL, id UUID NOT NULL, PRIMARY KEY (id))');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE "task"');
    }
}
