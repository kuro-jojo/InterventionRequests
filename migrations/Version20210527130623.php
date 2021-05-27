<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210527130623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE demande_intervention_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE pole_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE agent_maintenance (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE agent_maintenance_pole (agent_maintenance_id INT NOT NULL, pole_id INT NOT NULL, PRIMARY KEY(agent_maintenance_id, pole_id))');
        $this->addSql('CREATE INDEX IDX_9CFCC397A3CBE4C4 ON agent_maintenance_pole (agent_maintenance_id)');
        $this->addSql('CREATE INDEX IDX_9CFCC397419C3385 ON agent_maintenance_pole (pole_id)');
        $this->addSql('CREATE TABLE chef_pole (id INT NOT NULL, mon_pole_id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1AD5823147C6289 ON chef_pole (mon_pole_id)');
        $this->addSql('CREATE TABLE chef_service (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE demande_intervention (id INT NOT NULL, pole_concerne_id INT NOT NULL, nom_demandeur VARCHAR(255) NOT NULL, email_demandeur VARCHAR(255) NOT NULL, contact_demandeur VARCHAR(255) NOT NULL, fonction VARCHAR(255) NOT NULL, priorite VARCHAR(255) CHECK(priorite IN (\'Urgent\', \'PeuUrgent\', \'PasUrgent\')) NOT NULL, departement VARCHAR(255) CHECK(departement IN (\'BatimentDirectional\', \'BatimentACP\', \'GenieCivil\', \'Gestion\', \'GenieChimique\', \'GenieElec\', \'GenieMeca\', \'GenieInf\', \'RessourceHumaines\', \'Caisse\', \'LPAO\', \'LERG\', \'LMAGI\', \'LER\', \'SID\', \'Scolarite\', \'CRENT\', \'LAE\', \'LIMBI\', \'LIRT\', \'Autre\')) NOT NULL, causeDefaillance VARCHAR(255) CHECK(causeDefaillance IN (\'UsureNormal\', \'DefautUtilisateur\', \'DefautProduit\', \'Autres\')) NOT NULL, description TEXT DEFAULT NULL, date_demande TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, statut VARCHAR(255) CHECK(statut IN (\'EN_ATTENTE\', \'EN_COURS\', \'OK\')) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_86D186D1D1F1831B ON demande_intervention (pole_concerne_id)');
        $this->addSql('COMMENT ON COLUMN demande_intervention.priorite IS \'(DC2Type:Priorite)\'');
        $this->addSql('COMMENT ON COLUMN demande_intervention.departement IS \'(DC2Type:DepartementType)\'');
        $this->addSql('COMMENT ON COLUMN demande_intervention.causeDefaillance IS \'(DC2Type:CauseDefaillanceType)\'');
        $this->addSql('COMMENT ON COLUMN demande_intervention.statut IS \'(DC2Type:StatutType)\'');
        $this->addSql('CREATE TABLE demande_intervention_agent_maintenance (demande_intervention_id INT NOT NULL, agent_maintenance_id INT NOT NULL, PRIMARY KEY(demande_intervention_id, agent_maintenance_id))');
        $this->addSql('CREATE INDEX IDX_EDF185F97607473E ON demande_intervention_agent_maintenance (demande_intervention_id)');
        $this->addSql('CREATE INDEX IDX_EDF185F9A3CBE4C4 ON demande_intervention_agent_maintenance (agent_maintenance_id)');
        $this->addSql('CREATE TABLE pole (id INT NOT NULL, nom_pole VARCHAR(255) CHECK(nom_pole IN (\'Maconnerie\', \'Menuiserie\', \'Climatisation\', \'Electricite\', \'Plomberie\')) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN pole.nom_pole IS \'(DC2Type:PoleType)\'');
        $this->addSql('CREATE TABLE responsable (id INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE responsable_demande_intervention (responsable_id INT NOT NULL, demande_intervention_id INT NOT NULL, PRIMARY KEY(responsable_id, demande_intervention_id))');
        $this->addSql('CREATE INDEX IDX_5FA21E3953C59D72 ON responsable_demande_intervention (responsable_id)');
        $this->addSql('CREATE INDEX IDX_5FA21E397607473E ON responsable_demande_intervention (demande_intervention_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON "user" (email)');
        $this->addSql('ALTER TABLE agent_maintenance ADD CONSTRAINT FK_BF4AA3A9BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agent_maintenance_pole ADD CONSTRAINT FK_9CFCC397A3CBE4C4 FOREIGN KEY (agent_maintenance_id) REFERENCES agent_maintenance (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE agent_maintenance_pole ADD CONSTRAINT FK_9CFCC397419C3385 FOREIGN KEY (pole_id) REFERENCES pole (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chef_pole ADD CONSTRAINT FK_E1AD5823147C6289 FOREIGN KEY (mon_pole_id) REFERENCES pole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chef_pole ADD CONSTRAINT FK_E1AD5823BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE chef_service ADD CONSTRAINT FK_264E9C36BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE demande_intervention ADD CONSTRAINT FK_86D186D1D1F1831B FOREIGN KEY (pole_concerne_id) REFERENCES pole (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE demande_intervention_agent_maintenance ADD CONSTRAINT FK_EDF185F97607473E FOREIGN KEY (demande_intervention_id) REFERENCES demande_intervention (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE demande_intervention_agent_maintenance ADD CONSTRAINT FK_EDF185F9A3CBE4C4 FOREIGN KEY (agent_maintenance_id) REFERENCES agent_maintenance (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE responsable ADD CONSTRAINT FK_52520D07BF396750 FOREIGN KEY (id) REFERENCES "user" (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE responsable_demande_intervention ADD CONSTRAINT FK_5FA21E3953C59D72 FOREIGN KEY (responsable_id) REFERENCES responsable (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE responsable_demande_intervention ADD CONSTRAINT FK_5FA21E397607473E FOREIGN KEY (demande_intervention_id) REFERENCES demande_intervention (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE agent_maintenance_pole DROP CONSTRAINT FK_9CFCC397A3CBE4C4');
        $this->addSql('ALTER TABLE demande_intervention_agent_maintenance DROP CONSTRAINT FK_EDF185F9A3CBE4C4');
        $this->addSql('ALTER TABLE demande_intervention_agent_maintenance DROP CONSTRAINT FK_EDF185F97607473E');
        $this->addSql('ALTER TABLE responsable_demande_intervention DROP CONSTRAINT FK_5FA21E397607473E');
        $this->addSql('ALTER TABLE agent_maintenance_pole DROP CONSTRAINT FK_9CFCC397419C3385');
        $this->addSql('ALTER TABLE chef_pole DROP CONSTRAINT FK_E1AD5823147C6289');
        $this->addSql('ALTER TABLE demande_intervention DROP CONSTRAINT FK_86D186D1D1F1831B');
        $this->addSql('ALTER TABLE responsable_demande_intervention DROP CONSTRAINT FK_5FA21E3953C59D72');
        $this->addSql('ALTER TABLE agent_maintenance DROP CONSTRAINT FK_BF4AA3A9BF396750');
        $this->addSql('ALTER TABLE chef_pole DROP CONSTRAINT FK_E1AD5823BF396750');
        $this->addSql('ALTER TABLE chef_service DROP CONSTRAINT FK_264E9C36BF396750');
        $this->addSql('ALTER TABLE responsable DROP CONSTRAINT FK_52520D07BF396750');
        $this->addSql('DROP SEQUENCE demande_intervention_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE pole_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE agent_maintenance');
        $this->addSql('DROP TABLE agent_maintenance_pole');
        $this->addSql('DROP TABLE chef_pole');
        $this->addSql('DROP TABLE chef_service');
        $this->addSql('DROP TABLE demande_intervention');
        $this->addSql('DROP TABLE demande_intervention_agent_maintenance');
        $this->addSql('DROP TABLE pole');
        $this->addSql('DROP TABLE responsable');
        $this->addSql('DROP TABLE responsable_demande_intervention');
        $this->addSql('DROP TABLE "user"');
    }
}
