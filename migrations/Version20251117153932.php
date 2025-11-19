<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251117153932 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE advent_calendar (id INT AUTO_INCREMENT NOT NULL, year INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calendar ADD advent_calendar_id INT NOT NULL');
        $this->addSql('ALTER TABLE calendar ADD CONSTRAINT FK_6EA9A1468002FA4A FOREIGN KEY (advent_calendar_id) REFERENCES advent_calendar (id)');
        $this->addSql('CREATE INDEX IDX_6EA9A1468002FA4A ON calendar (advent_calendar_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calendar DROP FOREIGN KEY FK_6EA9A1468002FA4A');
        $this->addSql('DROP TABLE advent_calendar');
        $this->addSql('DROP INDEX IDX_6EA9A1468002FA4A ON calendar');
        $this->addSql('ALTER TABLE calendar DROP advent_calendar_id');
    }
}
