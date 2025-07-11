<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250711070108 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alerte (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(50) NOT NULL, message VARCHAR(255) NOT NULL, date_alerte DATETIME NOT NULL, resolue TINYINT(1) NOT NULL, collaborateur_id INT NOT NULL, INDEX IDX_3AE753AA848E3B1 (collaborateur_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE collaborateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, role VARCHAR(50) NOT NULL, charge_actuelle DOUBLE PRECISION NOT NULL, disponible TINYINT(1) NOT NULL, created_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_770CBCD3E7927C74 (email), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE collaborateur_competence (id INT AUTO_INCREMENT NOT NULL, niveau INT NOT NULL, collaborateur_id INT NOT NULL, competence_id INT NOT NULL, INDEX IDX_B5491697A848E3B1 (collaborateur_id), INDEX IDX_B549169715761DAB (competence_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, description LONGTEXT DEFAULT NULL, PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE historique_tache (id INT AUTO_INCREMENT NOT NULL, date_reaffectation DATETIME NOT NULL, raison LONGTEXT NOT NULL, tache_id INT NOT NULL, ancien_collab_id INT NOT NULL, nouveau_collab_id INT NOT NULL, INDEX IDX_DD07FA06D2235D39 (tache_id), INDEX IDX_DD07FA06A43190E3 (ancien_collab_id), INDEX IDX_DD07FA06FFCAD983 (nouveau_collab_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE mission (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, priorite INT NOT NULL, date_debut DATETIME NOT NULL, date_fin_prevue DATETIME NOT NULL, statut VARCHAR(255) NOT NULL, responsable_id INT NOT NULL, INDEX IDX_9067F23C53C59D72 (responsable_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE tache (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, charge_estimee DOUBLE PRECISION NOT NULL, charge_reelle DOUBLE PRECISION DEFAULT NULL, date_debut DATETIME NOT NULL, date_fin_prevue DATETIME NOT NULL, statut VARCHAR(50) NOT NULL, priorite INT NOT NULL, mission_id INT NOT NULL, collaborateur_id INT DEFAULT NULL, competence_requise_id INT NOT NULL, INDEX IDX_93872075BE6CAE90 (mission_id), INDEX IDX_93872075A848E3B1 (collaborateur_id), INDEX IDX_93872075F399F0D3 (competence_requise_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE alerte ADD CONSTRAINT FK_3AE753AA848E3B1 FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id)');
        $this->addSql('ALTER TABLE collaborateur_competence ADD CONSTRAINT FK_B5491697A848E3B1 FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id)');
        $this->addSql('ALTER TABLE collaborateur_competence ADD CONSTRAINT FK_B549169715761DAB FOREIGN KEY (competence_id) REFERENCES competence (id)');
        $this->addSql('ALTER TABLE historique_tache ADD CONSTRAINT FK_DD07FA06D2235D39 FOREIGN KEY (tache_id) REFERENCES tache (id)');
        $this->addSql('ALTER TABLE historique_tache ADD CONSTRAINT FK_DD07FA06A43190E3 FOREIGN KEY (ancien_collab_id) REFERENCES collaborateur (id)');
        $this->addSql('ALTER TABLE historique_tache ADD CONSTRAINT FK_DD07FA06FFCAD983 FOREIGN KEY (nouveau_collab_id) REFERENCES collaborateur (id)');
        $this->addSql('ALTER TABLE mission ADD CONSTRAINT FK_9067F23C53C59D72 FOREIGN KEY (responsable_id) REFERENCES collaborateur (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075A848E3B1 FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id)');
        $this->addSql('ALTER TABLE tache ADD CONSTRAINT FK_93872075F399F0D3 FOREIGN KEY (competence_requise_id) REFERENCES competence (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alerte DROP FOREIGN KEY FK_3AE753AA848E3B1');
        $this->addSql('ALTER TABLE collaborateur_competence DROP FOREIGN KEY FK_B5491697A848E3B1');
        $this->addSql('ALTER TABLE collaborateur_competence DROP FOREIGN KEY FK_B549169715761DAB');
        $this->addSql('ALTER TABLE historique_tache DROP FOREIGN KEY FK_DD07FA06D2235D39');
        $this->addSql('ALTER TABLE historique_tache DROP FOREIGN KEY FK_DD07FA06A43190E3');
        $this->addSql('ALTER TABLE historique_tache DROP FOREIGN KEY FK_DD07FA06FFCAD983');
        $this->addSql('ALTER TABLE mission DROP FOREIGN KEY FK_9067F23C53C59D72');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075BE6CAE90');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075A848E3B1');
        $this->addSql('ALTER TABLE tache DROP FOREIGN KEY FK_93872075F399F0D3');
        $this->addSql('DROP TABLE alerte');
        $this->addSql('DROP TABLE collaborateur');
        $this->addSql('DROP TABLE collaborateur_competence');
        $this->addSql('DROP TABLE competence');
        $this->addSql('DROP TABLE historique_tache');
        $this->addSql('DROP TABLE mission');
        $this->addSql('DROP TABLE tache');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
