<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210508003324 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_intervention CHANGE department departement ENUM(\'BatimentDirectional\', \'BatimentACP\', \'GenieCivil\', \'Gestion\', \'GenieChimique\', \'GenieElec\', \'GenieMeca\', \'GenieInf\', \'RessourceHumaines\', \'Caisse\', \'LPAO\', \'LERG\', \'LMAGI\', \'LER\', \'SID\', \'Scolarite\', \'CRENT\', \'LAE\', \'LIMBI\', \'LIRT\', \'Autre\') NOT NULL COMMENT \'(DC2Type:DepartementType)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_intervention CHANGE departement department ENUM(\'BatimentDirectional\', \'BatimentACP\', \'GenieCivil\', \'Gestion\', \'GenieChimique\', \'GenieElec\', \'GenieMeca\', \'GenieInf\', \'RessourceHumaines\', \'Caisse\', \'LPAO\', \'LERG\', \'LMAGI\', \'LER\', \'SID\', \'Scolarite\', \'CRENT\', \'LAE\', \'LIMBI\', \'LIRT\', \'Autre\') CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci` COMMENT \'(DC2Type:DepartementType)\'');
    }
}
