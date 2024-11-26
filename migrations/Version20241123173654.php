<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241123173654 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ranking_challenge DROP FOREIGN KEY FK_CA13F66C20F64684');
        $this->addSql('ALTER TABLE ranking_challenge DROP FOREIGN KEY FK_CA13F66C98A21AC6');
        $this->addSql('DROP TABLE ranking_challenge');
        $this->addSql('ALTER TABLE ranking ADD challenge_id INT NOT NULL');
        $this->addSql('ALTER TABLE ranking ADD CONSTRAINT FK_80B839D098A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id)');
        $this->addSql('CREATE INDEX IDX_80B839D098A21AC6 ON ranking (challenge_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ranking_challenge (ranking_id INT NOT NULL, challenge_id INT NOT NULL, INDEX IDX_CA13F66C98A21AC6 (challenge_id), INDEX IDX_CA13F66C20F64684 (ranking_id), PRIMARY KEY(ranking_id, challenge_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ranking_challenge ADD CONSTRAINT FK_CA13F66C20F64684 FOREIGN KEY (ranking_id) REFERENCES ranking (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ranking_challenge ADD CONSTRAINT FK_CA13F66C98A21AC6 FOREIGN KEY (challenge_id) REFERENCES challenge (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE ranking DROP FOREIGN KEY FK_80B839D098A21AC6');
        $this->addSql('DROP INDEX IDX_80B839D098A21AC6 ON ranking');
        $this->addSql('ALTER TABLE ranking DROP challenge_id');
    }
}
