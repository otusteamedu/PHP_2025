<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250609121448 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE categories (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_3AF346685E237E06 ON categories (name)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE news (id SERIAL NOT NULL, category_id INT DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, title VARCHAR(255) NOT NULL, author VARCHAR(255) NOT NULL, content VARCHAR(3000) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1DD3995012469DE2 ON news (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            COMMENT ON COLUMN news.created_at IS '(DC2Type:datetime_immutable)'
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE subscriptions (id SERIAL NOT NULL, category_id INT DEFAULT NULL, email VARCHAR(50) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4778A0112469DE2 ON subscriptions (category_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news ADD CONSTRAINT FK_1DD3995012469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscriptions ADD CONSTRAINT FK_4778A0112469DE2 FOREIGN KEY (category_id) REFERENCES categories (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE news DROP CONSTRAINT FK_1DD3995012469DE2
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE subscriptions DROP CONSTRAINT FK_4778A0112469DE2
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE categories
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE news
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE subscriptions
        SQL);
    }
}
