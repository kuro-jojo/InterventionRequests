<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210506160001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE agent_maintenance_pole (agent_maintenance_id INT NOT NULL, pole_id INT NOT NULL, INDEX IDX_9CFCC397A3CBE4C4 (agent_maintenance_id), INDEX IDX_9CFCC397419C3385 (pole_id), PRIMARY KEY(agent_maintenance_id, pole_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE demande_intervention_agent_maintenance (demande_intervention_id INT NOT NULL, agent_maintenance_id INT NOT NULL, INDEX IDX_EDF185F97607473E (demande_intervention_id), INDEX IDX_EDF185F9A3CBE4C4 (agent_maintenance_id), PRIMARY KEY(demande_intervention_id, agent_maintenance_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE responsable_demande_intervention (responsable_id INT NOT NULL, demande_intervention_id INT NOT NULL, INDEX IDX_5FA21E3953C59D72 (responsable_id), INDEX IDX_5FA21E397607473E (demande_intervention_id), PRIMARY KEY(responsable_id, demande_intervention_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE agent_maintenance_pole ADD CONSTRAINT FK_9CFCC397A3CBE4C4 FOREIGN KEY (agent_maintenance_id) REFERENCES agent_maintenance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE agent_maintenance_pole ADD CONSTRAINT FK_9CFCC397419C3385 FOREIGN KEY (pole_id) REFERENCES pole (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demande_intervention_agent_maintenance ADD CONSTRAINT FK_EDF185F97607473E FOREIGN KEY (demande_intervention_id) REFERENCES demande_intervention (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE demande_intervention_agent_maintenance ADD CONSTRAINT FK_EDF185F9A3CBE4C4 FOREIGN KEY (agent_maintenance_id) REFERENCES agent_maintenance (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE responsable_demande_intervention ADD CONSTRAINT FK_5FA21E3953C59D72 FOREIGN KEY (responsable_id) REFERENCES responsable (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE responsable_demande_intervention ADD CONSTRAINT FK_5FA21E397607473E FOREIGN KEY (demande_intervention_id) REFERENCES demande_intervention (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE chef_pole ADD mon_pole_id INT NOT NULL');
        $this->addSql('ALTER TABLE chef_pole ADD CONSTRAINT FK_E1AD5823147C6289 FOREIGN KEY (mon_pole_id) REFERENCES pole (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_E1AD5823147C6289 ON chef_pole (mon_pole_id)');
        $this->addSql('ALTER TABLE demande_intervention ADD pole_concerne_id INT NOT NULL');
        $this->addSql('ALTER TABLE demande_intervention ADD CONSTRAINT FK_86D186D1D1F1831B FOREIGN KEY (pole_concerne_id) REFERENCES pole (id)');
        $this->addSql('CREATE INDEX IDX_86D186D1D1F1831B ON demande_intervention (pole_concerne_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE agent_maintenance_pole');
        $this->addSql('DROP TABLE demande_intervention_agent_maintenance');
        $this->addSql('DROP TABLE responsable_demande_intervention');
        $this->addSql('ALTER TABLE chef_pole DROP FOREIGN KEY FK_E1AD5823147C6289');
        $this->addSql('DROP INDEX UNIQ_E1AD5823147C6289 ON chef_pole');
        $this->addSql('ALTER TABLE chef_pole DROP mon_pole_id');
        $this->addSql('ALTER TABLE demande_intervention DROP FOREIGN KEY FK_86D186D1D1F1831B');
        $this->addSql('DROP INDEX IDX_86D186D1D1F1831B ON demande_intervention');
        $this->addSql('ALTER TABLE demande_intervention DROP pole_concerne_id');
    }
}
