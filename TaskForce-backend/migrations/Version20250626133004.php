<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250626133004 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE affectation (id INT AUTO_INCREMENT NOT NULL, collaborateur_id INT NOT NULL, mission_id INT NOT NULL, role VARCHAR(255) NOT NULL, date_affectation DATE NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, INDEX IDX_F4DD61D3A848E3B1 (collaborateur_id), INDEX IDX_F4DD61D3BE6CAE90 (mission_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE collaborateur (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, poste VARCHAR(255) NOT NULL, actif TINYINT(1) NOT NULL, date_naissance DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE collaborateur_competence (collaborateur_id INT NOT NULL, competence_id INT NOT NULL, INDEX IDX_B5491697A848E3B1 (collaborateur_id), INDEX IDX_B549169715761DAB (competence_id), PRIMARY KEY(collaborateur_id, competence_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE competence (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE mission (id INT AUTO_INCREMENT NOT NULL, titre VARCHAR(255) NOT NULL, description VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, nom VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, competences_requises VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3A848E3B1 FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation ADD CONSTRAINT FK_F4DD61D3BE6CAE90 FOREIGN KEY (mission_id) REFERENCES mission (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collaborateur_competence ADD CONSTRAINT FK_B5491697A848E3B1 FOREIGN KEY (collaborateur_id) REFERENCES collaborateur (id) ON DELETE CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collaborateur_competence ADD CONSTRAINT FK_B549169715761DAB FOREIGN KEY (competence_id) REFERENCES competence (id) ON DELETE CASCADE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3A848E3B1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE affectation DROP FOREIGN KEY FK_F4DD61D3BE6CAE90
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collaborateur_competence DROP FOREIGN KEY FK_B5491697A848E3B1
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE collaborateur_competence DROP FOREIGN KEY FK_B549169715761DAB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE affectation
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE collaborateur
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE collaborateur_competence
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE competence
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE mission
        SQL);
    }
}
