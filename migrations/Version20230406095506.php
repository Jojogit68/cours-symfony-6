<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230406095506 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE tag (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tag_annonce (tag_id INT NOT NULL, annonce_id INT NOT NULL, INDEX IDX_C464BE4FBAD26311 (tag_id), INDEX IDX_C464BE4F8805AB2F (annonce_id), PRIMARY KEY(tag_id, annonce_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE tag_annonce ADD CONSTRAINT FK_C464BE4FBAD26311 FOREIGN KEY (tag_id) REFERENCES tag (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE tag_annonce ADD CONSTRAINT FK_C464BE4F8805AB2F FOREIGN KEY (annonce_id) REFERENCES annonce (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tag_annonce DROP FOREIGN KEY FK_C464BE4FBAD26311');
        $this->addSql('ALTER TABLE tag_annonce DROP FOREIGN KEY FK_C464BE4F8805AB2F');
        $this->addSql('DROP TABLE tag');
        $this->addSql('DROP TABLE tag_annonce');
    }
}
