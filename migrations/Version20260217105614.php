<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260217105614 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, occurred_at, description, type, place, created_at, updated_at, author FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, occurred_at DATETIME NOT NULL, description VARCHAR(500) NOT NULL, type VARCHAR(50) NOT NULL, place VARCHAR(100) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL, author VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO event (id, occurred_at, description, type, place, created_at, updated_at, author) SELECT id, occurred_at, description, type, place, created_at, updated_at, author FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TEMPORARY TABLE __temp__event AS SELECT id, occurred_at, description, type, place, author, created_at, updated_at FROM event');
        $this->addSql('DROP TABLE event');
        $this->addSql('CREATE TABLE event (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, occurred_at DATETIME NOT NULL, description VARCHAR(500) NOT NULL, type VARCHAR(50) NOT NULL, place VARCHAR(100) NOT NULL, author VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME DEFAULT NULL)');
        $this->addSql('INSERT INTO event (id, occurred_at, description, type, place, author, created_at, updated_at) SELECT id, occurred_at, description, type, place, author, created_at, updated_at FROM __temp__event');
        $this->addSql('DROP TABLE __temp__event');
    }
}
