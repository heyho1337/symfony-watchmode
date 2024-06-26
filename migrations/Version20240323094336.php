<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240323094336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE users ADD roles JSON NOT NULL COMMENT \'(DC2Type:json)\', CHANGE user_name user_name VARCHAR(180) NOT NULL, CHANGE user_email user_email VARCHAR(180) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_IDENTIFIER_USER_NAME ON users (user_name)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX UNIQ_IDENTIFIER_USER_NAME ON users');
        $this->addSql('ALTER TABLE users DROP roles, CHANGE user_name user_name VARCHAR(50) NOT NULL, CHANGE user_email user_email VARCHAR(100) NOT NULL');
    }
}
