<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251123082857 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE hangman_challenge (id INT NOT NULL, word VARCHAR(255) NOT NULL, hint VARCHAR(255) DEFAULT NULL, max_errors INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE hangman_challenge ADD CONSTRAINT FK_CFBEA07BF396750 FOREIGN KEY (id) REFERENCES challenge (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE hangman_challenge DROP FOREIGN KEY FK_CFBEA07BF396750');
        $this->addSql('DROP TABLE hangman_challenge');
    }
}
