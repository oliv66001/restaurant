<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230221131442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE dessert (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE dessert_categories (dessert_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_2A19495B745B52FD (dessert_id), INDEX IDX_2A19495BA21214B7 (categories_id), PRIMARY KEY(dessert_id, categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drink (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE drink_categories (drink_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_1270881536AA4BB4 (drink_id), INDEX IDX_12708815A21214B7 (categories_id), PRIMARY KEY(drink_id, categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entree (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE entree_categories (entree_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_41548C9DAF7BD910 (entree_id), INDEX IDX_41548C9DA21214B7 (categories_id), PRIMARY KEY(entree_id, categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plat (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(150) NOT NULL, description LONGTEXT NOT NULL, price INT NOT NULL, slug VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plat_categories (plat_id INT NOT NULL, categories_id INT NOT NULL, INDEX IDX_B1B179E3D73DB560 (plat_id), INDEX IDX_B1B179E3A21214B7 (categories_id), PRIMARY KEY(plat_id, categories_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE dessert_categories ADD CONSTRAINT FK_2A19495B745B52FD FOREIGN KEY (dessert_id) REFERENCES dessert (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE dessert_categories ADD CONSTRAINT FK_2A19495BA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE drink_categories ADD CONSTRAINT FK_1270881536AA4BB4 FOREIGN KEY (drink_id) REFERENCES drink (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE drink_categories ADD CONSTRAINT FK_12708815A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entree_categories ADD CONSTRAINT FK_41548C9DAF7BD910 FOREIGN KEY (entree_id) REFERENCES entree (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE entree_categories ADD CONSTRAINT FK_41548C9DA21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plat_categories ADD CONSTRAINT FK_B1B179E3D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE plat_categories ADD CONSTRAINT FK_B1B179E3A21214B7 FOREIGN KEY (categories_id) REFERENCES categories (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE images CHANGE dishes_id dishes_id INT NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dessert_categories DROP FOREIGN KEY FK_2A19495B745B52FD');
        $this->addSql('ALTER TABLE dessert_categories DROP FOREIGN KEY FK_2A19495BA21214B7');
        $this->addSql('ALTER TABLE drink_categories DROP FOREIGN KEY FK_1270881536AA4BB4');
        $this->addSql('ALTER TABLE drink_categories DROP FOREIGN KEY FK_12708815A21214B7');
        $this->addSql('ALTER TABLE entree_categories DROP FOREIGN KEY FK_41548C9DAF7BD910');
        $this->addSql('ALTER TABLE entree_categories DROP FOREIGN KEY FK_41548C9DA21214B7');
        $this->addSql('ALTER TABLE plat_categories DROP FOREIGN KEY FK_B1B179E3D73DB560');
        $this->addSql('ALTER TABLE plat_categories DROP FOREIGN KEY FK_B1B179E3A21214B7');
        $this->addSql('DROP TABLE dessert');
        $this->addSql('DROP TABLE dessert_categories');
        $this->addSql('DROP TABLE drink');
        $this->addSql('DROP TABLE drink_categories');
        $this->addSql('DROP TABLE entree');
        $this->addSql('DROP TABLE entree_categories');
        $this->addSql('DROP TABLE plat');
        $this->addSql('DROP TABLE plat_categories');
        $this->addSql('ALTER TABLE images CHANGE dishes_id dishes_id INT DEFAULT NULL');
    }
}
