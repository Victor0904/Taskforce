<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250826172400 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout des colonnes manquantes pour l\'entité User';
    }

    public function up(Schema $schema): void
    {
        // Ajouter les colonnes si elles n'existent pas déjà
        $this->addSql('ALTER TABLE user ADD COLUMN created_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN expires_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE user ADD COLUMN is_active BOOLEAN DEFAULT 1');
        $this->addSql('ALTER TABLE user ADD COLUMN must_change_password BOOLEAN DEFAULT 1');
        
        // Mettre à jour les enregistrements existants
        $this->addSql('UPDATE user SET created_at = datetime("now"), expires_at = datetime("now", "+3 days"), is_active = 1, must_change_password = 1 WHERE created_at IS NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE user DROP created_at, DROP expires_at, DROP is_active, DROP must_change_password');
    }
}
