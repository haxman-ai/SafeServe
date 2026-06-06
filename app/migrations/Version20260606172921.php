<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260606172921 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE temp ADD plat_id INT NOT NULL, ADD user_id INT NOT NULL, DROP produit, DROP initials, CHANGE heure releve_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE temp ADD CONSTRAINT FK_B5385CAD73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id)');
        $this->addSql('ALTER TABLE temp ADD CONSTRAINT FK_B5385CAA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_B5385CAD73DB560 ON temp (plat_id)');
        $this->addSql('CREATE INDEX IDX_B5385CAA76ED395 ON temp (user_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE temp DROP FOREIGN KEY FK_B5385CAD73DB560');
        $this->addSql('ALTER TABLE temp DROP FOREIGN KEY FK_B5385CAA76ED395');
        $this->addSql('DROP INDEX IDX_B5385CAD73DB560 ON temp');
        $this->addSql('DROP INDEX IDX_B5385CAA76ED395 ON temp');
        $this->addSql('ALTER TABLE temp ADD produit VARCHAR(100) NOT NULL, ADD initials VARCHAR(10) NOT NULL, DROP plat_id, DROP user_id, CHANGE releve_at heure DATETIME NOT NULL');
    }
}
