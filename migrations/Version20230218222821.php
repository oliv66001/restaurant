<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230218222821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images ADD dishes_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE images ADD CONSTRAINT FK_E01FBE6AA05DD37A FOREIGN KEY (dishes_id) REFERENCES dishes (id)');
        $this->addSql('CREATE INDEX IDX_E01FBE6AA05DD37A ON images (dishes_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE images DROP FOREIGN KEY FK_E01FBE6AA05DD37A');
        $this->addSql('DROP INDEX IDX_E01FBE6AA05DD37A ON images');
        $this->addSql('ALTER TABLE images DROP dishes_id');
    }
}
