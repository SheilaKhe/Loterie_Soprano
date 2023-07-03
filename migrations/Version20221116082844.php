<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221116082844 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE code CHANGE stock stock INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lot ADD win_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE lot ADD CONSTRAINT FK_B81291B713E15F4 FOREIGN KEY (win_id) REFERENCES result (id)');
        $this->addSql('CREATE INDEX IDX_B81291B713E15F4 ON lot (win_id)');
        $this->addSql('ALTER TABLE winner CHANGE date date DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE code CHANGE stock stock INT DEFAULT 0 NOT NULL');
        $this->addSql('ALTER TABLE lot DROP FOREIGN KEY FK_B81291B713E15F4');
        $this->addSql('DROP INDEX IDX_B81291B713E15F4 ON lot');
        $this->addSql('ALTER TABLE lot DROP win_id');
        $this->addSql('ALTER TABLE winner CHANGE date date DATETIME NOT NULL');
    }
}
