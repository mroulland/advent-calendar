<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251116104007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation_challenge CHANGE questions questions JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE participation_challenge ADD CONSTRAINT FK_B98EA73ABF396750 FOREIGN KEY (id) REFERENCES challenge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE quiz_challenge CHANGE questions questions JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE ranking CHANGE details details JSON DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD token_hash VARCHAR(255) DEFAULT NULL, ADD token_hash_expires_at DATETIME DEFAULT NULL, CHANGE roles roles JSON NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE participation_challenge DROP FOREIGN KEY FK_B98EA73ABF396750');
        $this->addSql('ALTER TABLE participation_challenge CHANGE questions questions JSON DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE quiz_challenge CHANGE questions questions JSON DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE ranking CHANGE details details JSON DEFAULT NULL COLLATE `utf8mb4_bin`');
        $this->addSql('ALTER TABLE `user` DROP token_hash, DROP token_hash_expires_at, CHANGE roles roles JSON NOT NULL COLLATE `utf8mb4_bin`');
    }
}
