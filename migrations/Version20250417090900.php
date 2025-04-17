<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250417090900 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP SEQUENCE countries_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE teachers_id_seq CASCADE');
        $this->addSql('CREATE TABLE news (id SERIAL NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) DEFAULT NULL, create_date TIMESTAMP(0) WITH TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE countries');
        $this->addSql('DROP TABLE teachers');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('CREATE SEQUENCE countries_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE teachers_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE countries (id SERIAL NOT NULL, name VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE teachers (id SERIAL NOT NULL, age INT DEFAULT NULL, gender VARCHAR(255) DEFAULT NULL, name VARCHAR(255) DEFAULT NULL, rating INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('DROP TABLE news');
    }
}
