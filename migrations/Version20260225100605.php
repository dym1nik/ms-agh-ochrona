<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260225100605 extends AbstractMigration
{
   

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql("ALTER TABLE event ADD status VARCHAR(32) DEFAULT 'new' NOT NULL");
        $this->addSql("UPDATE event SET status = 'new' WHERE status IS NULL OR status = ''");
    }

    
}
