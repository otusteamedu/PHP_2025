<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20251119201109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Добавление статуса';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE task ADD status VARCHAR(255) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE "task" DROP status');
    }
}
