<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20250622121157 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Création des tables collaborateur, mission et affectation avec leurs relations';
    }

    public function up(Schema $schema): void
    {
        // Table Collaborateur
        $this->addSql('CREATE TABLE collaborateur (
            id INT AUTO_INCREMENT NOT NULL,
            nom VARCHAR(255) NOT NULL,
            prenom VARCHAR(255) NOT NULL,
            email VARCHAR(255) NOT NULL,
            poste VARCHAR(255) NOT NULL,
            actif TINYINT(1) NOT NULL,
            date_naissance DATE NOT NULL,
            PRIMARY KEY(id)
        )');

        // Table Mission
        $this->addSql('CREATE TABLE mission (
            id INT AUTO_INCREMENT NOT NULL,
            titre VARCHAR(255) NOT NULL,
            description VARCHAR(255) NOT NULL,
            date_debut DATE NOT NULL,
            date_fin DATE NOT NULL,
            nom VARCHAR(255) NOT NULL,
            statut VARCHAR(255) NOT NULL,
            competences_requises VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
        )');

        // Table Affectation
        $this->addSql('CREATE TABLE affectation (
            id INT AUTO_INCREMENT NOT NULL,
            collaborateur_id INT NOT NULL,
            mission_id INT NOT NULL,
            role VARCHAR(255) NOT NULL,
            date_affectation DATE NOT NULL,
            date_debut DATE NOT NULL,
            date_fin DATE NOT NULL,
            INDEX IDX_AFFECTATION_COLLABORATEUR (collaborateur_id),
            INDEX IDX_AFFECTATION_MISSION (mission_id),
            PRIMARY KEY(id)
        )');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_AFFECTATION_COLLABORATEUR FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id)');
        $this->addSql('ALTER TABLE affectation ADD CONSTRAINT FK_AFFECTATION_MISSION FOREIGN KEY (mission_id) REFERENCES mission (id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_AFFECTATION_COLLABORATEUR');
        $this->addSql('ALTER TABLE affectation DROP FOREIGN KEY FK_AFFECTATION_MISSION');
        $this->addSql('DROP TABLE affectation');
        $this->addSql('DROP TABLE collaborateur');
        $this->addSql('DROP TABLE mission');
    }
}
