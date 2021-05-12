<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210511234050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_intervention ADD statut ENUM(\'EN_ATTENTE\', \'EN_COURS\', \'OK\') NOT NULL COMMENT \'(DC2Type:StatutType)\'');
        $this->addSql('ALTER TABLE pole CHANGE nom_pole nom_pole ENUM(\'Maconnerie\', \'Menuiserie\', \'Climatisation\', \'Electricite\', \'Plomberie\') NOT NULL COMMENT \'(DC2Type:PoleType)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_intervention DROP statut');
        $this->addSql('ALTER TABLE pole CHANGE nom_pole nom_pole VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}
