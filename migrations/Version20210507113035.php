<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210507113035 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_intervention ADD priorite ENUM(\'Urgent\', \'PeuUrgent\', \'PasUrgent\') NOT NULL COMMENT \'(DC2Type:Priorite)\', ADD department ENUM(\'BatimentDirectional\', \'BatimentACP\', \'GenieCivil\', \'Gestion\', \'GenieChimique\', \'GenieElec\', \'GenieMeca\', \'GenieInf\', \'RessourceHumaines\', \'Caisse\', \'LPAO\', \'LERG\', \'LMAGI\', \'LER\', \'SID\', \'Scolarite\', \'CRENT\', \'LAE\', \'LIMBI\', \'LIRT\', \'Autre\') NOT NULL COMMENT \'(DC2Type:DepartementType)\', ADD causeDefaillance ENUM(\'UsureNormal\', \'DefautUtilisateur\', \'DefautProduit\', \'Autres\') NOT NULL COMMENT \'(DC2Type:CauseDefaillanceType)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE demande_intervention DROP priorite, DROP department, DROP causeDefaillance');
    }
}
