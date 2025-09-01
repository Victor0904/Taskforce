<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250818183920 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Ajouter les colonnes avec des valeurs par défaut
        $this->addSql('ALTER TABLE user ADD created_at DATETIME DEFAULT NULL, ADD expires_at DATETIME DEFAULT NULL, ADD is_active TINYINT(1) DEFAULT 1, ADD must_change_password TINYINT(1) DEFAULT 1');
        
        // Mettre à jour les enregistrements existants
        $this->addSql('UPDATE user SET created_at = NOW(), expires_at = DATE_ADD(NOW(), INTERVAL 3 DAY), is_active = 1, must_change_password = 1 WHERE created_at IS NULL');
        
        // Rendre les colonnes NOT NULL après la mise à jour
        $this->addSql('ALTER TABLE user MODIFY created_at DATETIME NOT NULL, MODIFY is_active TINYINT(1) NOT NULL, MODIFY must_change_password TINYINT(1) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP created_at, DROP expires_at, DROP is_active, DROP must_change_password');
    }
}
