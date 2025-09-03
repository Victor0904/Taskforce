<?php

namespace App\Tests\Entity;

use App\Entity\Tache;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use App\Entity\Mission;
use App\Entity\CollaborateurCompetence;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Validation;

class EntityTest extends TestCase
{
    public function testTacheEntity(): void
    {
        // Arrange
        $tache = new Tache();
        $mission = new Mission();
        $competence = new Competence();
        $collaborateur = new Collaborateur();

        // Act
        $tache->setTitre('Tâche de test')
              ->setDescription('Description de la tâche de test')
              ->setChargeEstimee(15.5)
              ->setChargeReelle(12.0)
              ->setDateDebut(new \DateTime('2024-01-01'))
              ->setDateFinPrevue(new \DateTime('2024-01-15'))
              ->setStatut('en_cours')
              ->setPriorite(2)
              ->setMission($mission)
              ->setCompetenceRequise($competence)
              ->setCollaborateur($collaborateur);

        // Assert
        $this->assertEquals('Tâche de test', $tache->getTitre());
        $this->assertEquals('Description de la tâche de test', $tache->getDescription());
        $this->assertEquals(15.5, $tache->getChargeEstimee());
        $this->assertEquals(12.0, $tache->getChargeReelle());
        $this->assertEquals('2024-01-01', $tache->getDateDebut()->format('Y-m-d'));
        $this->assertEquals('2024-01-15', $tache->getDateFinPrevue()->format('Y-m-d'));
        $this->assertEquals('en_cours', $tache->getStatut());
        $this->assertEquals(2, $tache->getPriorite());
        $this->assertSame($mission, $tache->getMission());
        $this->assertSame($competence, $tache->getCompetenceRequise());
        $this->assertSame($collaborateur, $tache->getCollaborateur());
    }

    public function testCollaborateurEntity(): void
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $competence = new Competence();
        $collabComp = new CollaborateurCompetence();

        // Act
        $collaborateur->setNom('Dupont')
                     ->setPrenom('Jean')
                     ->setEmail('jean.dupont@example.com')
                     ->setRole('Collaborateur')
                     ->setChargeActuelle(25.5)
                     ->setDisponible(true);

        $competence->setNom('PHP');
        $collabComp->setCompetence($competence)
                   ->setNiveau(8);

        $collaborateur->addCompetence($collabComp);

        // Assert
        $this->assertEquals('Dupont', $collaborateur->getNom());
        $this->assertEquals('Jean', $collaborateur->getPrenom());
        $this->assertEquals('jean.dupont@example.com', $collaborateur->getEmail());
        $this->assertEquals('Collaborateur', $collaborateur->getRole());
        $this->assertEquals(25.5, $collaborateur->getChargeActuelle());
        $this->assertTrue($collaborateur->isDisponible());
        $this->assertCount(1, $collaborateur->getCompetences());
        $this->assertTrue($collaborateur->getCompetences()->contains($collabComp));
    }

    public function testCollaborateurCompetenceManagement(): void
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $competence1 = new Competence();
        $competence2 = new Competence();
        $collabComp1 = new CollaborateurCompetence();
        $collabComp2 = new CollaborateurCompetence();

        $competence1->setNom('PHP');
        $competence2->setNom('JavaScript');
        
        $collabComp1->setCompetence($competence1)->setNiveau(8);
        $collabComp2->setCompetence($competence2)->setNiveau(6);

        // Act
        $collaborateur->addCompetence($collabComp1);
        $collaborateur->addCompetence($collabComp2);

        // Assert
        $this->assertCount(2, $collaborateur->getCompetences());
        $this->assertTrue($collaborateur->getCompetences()->contains($collabComp1));
        $this->assertTrue($collaborateur->getCompetences()->contains($collabComp2));

        // Test suppression
        $collaborateur->removeCompetence($collabComp1);
        $this->assertCount(1, $collaborateur->getCompetences());
        $this->assertFalse($collaborateur->getCompetences()->contains($collabComp1));
        $this->assertTrue($collaborateur->getCompetences()->contains($collabComp2));
    }

    public function testCompetenceEntity(): void
    {
        // Arrange
        $competence = new Competence();

        // Act
        $competence->setNom('Python')
                  ->setDescription('Compétence en programmation Python');

        // Assert
        $this->assertEquals('Python', $competence->getNom());
        $this->assertEquals('Compétence en programmation Python', $competence->getDescription());
    }

    public function testMissionEntity(): void
    {
        // Arrange
        $mission = new Mission();

        // Act
        $mission->setTitre('Projet Web')
               ->setDescription('Développement d\'une application web')
               ->setDateDebut(new \DateTime('2024-01-01'))
               ->setDateFinPrevue(new \DateTime('2024-06-30'))
               ->setStatut('en_cours');

        // Assert
        $this->assertEquals('Projet Web', $mission->getTitre());
        $this->assertEquals('Développement d\'une application web', $mission->getDescription());
        $this->assertEquals('2024-01-01', $mission->getDateDebut()->format('Y-m-d'));
        $this->assertEquals('2024-06-30', $mission->getDateFinPrevue()->format('Y-m-d'));
        $this->assertEquals('en_cours', $mission->getStatut());
    }

    public function testUserEntity(): void
    {
        // Arrange
        $user = new User();

        // Act
        $user->setEmail('test@example.com')
             ->setPassword('hashed_password')
             ->setRoles(['ROLE_USER'])
             ->setIsActive(true)
             ->setMustChangePassword(true)
             ->setExpiresAt(new \DateTime('+3 days'));

        // Assert
        $this->assertEquals('test@example.com', $user->getEmail());
        $this->assertEquals('hashed_password', $user->getPassword());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
        $this->assertTrue($user->getIsActive());
        $this->assertTrue($user->getMustChangePassword());
        $this->assertInstanceOf(\DateTime::class, $user->getExpiresAt());
    }

    public function testUserTemporaryAccess(): void
    {
        // Arrange
        $user = new User();
        $user->setEmail('temp@example.com')
             ->setPassword('hashed_password')
             ->setRoles(['ROLE_USER'])
             ->setIsActive(true)
             ->setMustChangePassword(true);

        // Test accès temporaire valide
        $user->setExpiresAt(new \DateTime('+1 day'));
        $this->assertTrue($user->isTemporaryAccess());

        // Test accès temporaire expiré
        $user->setExpiresAt(new \DateTime('-1 day'));
        $this->assertFalse($user->isTemporaryAccess());

        // Test accès permanent (pas de mot de passe à changer)
        $user->setMustChangePassword(false);
        $this->assertFalse($user->isTemporaryAccess());
    }

    public function testCollaborateurValidation(): void
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $validator = Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->getValidator();

        // Test avec données valides
        $collaborateur->setNom('Dupont')
                     ->setPrenom('Jean')
                     ->setEmail('jean.dupont@example.com')
                     ->setRole('Collaborateur');

        $violations = $validator->validate($collaborateur);
        $this->assertCount(0, $violations);

        // Test avec rôle invalide
        $collaborateur->setRole('Rôle invalide');
        $violations = $validator->validate($collaborateur);
        $this->assertCount(1, $violations);
        $this->assertStringContainsString('The value you selected is not a valid choice', $violations[0]->getMessage());

        // Test avec rôle vide
        $collaborateur->setRole('');
        $violations = $validator->validate($collaborateur);
        $this->assertGreaterThanOrEqual(1, count($violations)); // Au moins une violation
    }

    public function testTacheRelations(): void
    {
        // Arrange
        $tache = new Tache();
        $mission = new Mission();
        $competence = new Competence();
        $collaborateur = new Collaborateur();

        $mission->setTitre('Mission Test');
        $competence->setNom('PHP');
        $collaborateur->setNom('Dupont')->setPrenom('Jean');

        // Act
        $tache->setMission($mission)
              ->setCompetenceRequise($competence)
              ->setCollaborateur($collaborateur);

        // Assert
        $this->assertSame($mission, $tache->getMission());
        $this->assertSame($competence, $tache->getCompetenceRequise());
        $this->assertSame($collaborateur, $tache->getCollaborateur());
    }

    public function testEntityFluentInterface(): void
    {
        // Test que les setters retournent l'instance pour le fluent interface
        $tache = new Tache();
        $result = $tache->setTitre('Test')
                       ->setDescription('Description')
                       ->setChargeEstimee(10.0);

        $this->assertSame($tache, $result);

        $collaborateur = new Collaborateur();
        $result = $collaborateur->setNom('Test')
                               ->setPrenom('User')
                               ->setEmail('test@example.com');

        $this->assertSame($collaborateur, $result);
    }

    public function testEntityDefaultValues(): void
    {
        // Test des valeurs par défaut
        $collaborateur = new Collaborateur();
        $this->assertTrue($collaborateur->isDisponible());
        $this->assertEquals(0, $collaborateur->getChargeActuelle());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $collaborateur->getCompetences());
        $this->assertCount(0, $collaborateur->getCompetences());

        $user = new User();
        $this->assertTrue($user->getIsActive()); // L'entité User a isActive = true par défaut
        $this->assertTrue($user->getMustChangePassword()); // L'entité User a mustChangePassword = true par défaut
        $this->assertNotNull($user->getExpiresAt()); // L'entité User a expiresAt défini par défaut
    }
}
