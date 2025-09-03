<?php

namespace App\Tests\Repository;

use App\Entity\Tache;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use App\Entity\Mission;
use App\Repository\TacheRepository;
use App\Repository\CollaborateurRepository;
use App\Repository\CompetenceRepository;
use App\Repository\MissionRepository;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RepositoryTest extends KernelTestCase
{
    private TacheRepository $tacheRepository;
    private CollaborateurRepository $collaborateurRepository;
    private CompetenceRepository $competenceRepository;
    private MissionRepository $missionRepository;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $container = $kernel->getContainer();
        
        $this->tacheRepository = $container->get('doctrine')->getRepository(Tache::class);
        $this->collaborateurRepository = $container->get('doctrine')->getRepository(Collaborateur::class);
        $this->competenceRepository = $container->get('doctrine')->getRepository(Competence::class);
        $this->missionRepository = $container->get('doctrine')->getRepository(Mission::class);
    }

    public function testTacheRepositoryFindByMission(): void
    {
        // Arrange
        $mission = $this->createTestMission();
        $tache1 = $this->createTestTache('Tâche 1', $mission);
        $tache2 = $this->createTestTache('Tâche 2', $mission);
        $tache3 = $this->createTestTache('Tâche 3', null); // Tâche sans mission

        // Act
        $taches = $this->tacheRepository->findBy(['mission' => $mission]);

        // Assert
        $this->assertCount(2, $taches);
        $this->assertContains($tache1, $taches);
        $this->assertContains($tache2, $taches);
        $this->assertNotContains($tache3, $taches);
    }

    public function testTacheRepositoryFindByCollaborateur(): void
    {
        // Arrange
        $collaborateur = $this->createTestCollaborateur();
        $tache1 = $this->createTestTache('Tâche 1', null, $collaborateur);
        $tache2 = $this->createTestTache('Tâche 2', null, $collaborateur);
        $tache3 = $this->createTestTache('Tâche 3'); // Tâche sans collaborateur

        // Act
        $taches = $this->tacheRepository->findBy(['collaborateur' => $collaborateur]);

        // Assert
        $this->assertCount(2, $taches);
        $this->assertContains($tache1, $taches);
        $this->assertContains($tache2, $taches);
        $this->assertNotContains($tache3, $taches);
    }

    public function testTacheRepositoryFindByStatut(): void
    {
        // Arrange
        $tache1 = $this->createTestTache('Tâche en cours', null, null, 'en_cours');
        $tache2 = $this->createTestTache('Tâche terminée', null, null, 'terminee');
        $tache3 = $this->createTestTache('Tâche planifiée', null, null, 'planifiee');

        // Act
        $tachesEnCours = $this->tacheRepository->findBy(['statut' => 'en_cours']);
        $tachesTerminees = $this->tacheRepository->findBy(['statut' => 'terminee']);

        // Assert
        $this->assertCount(1, $tachesEnCours);
        $this->assertContains($tache1, $tachesEnCours);
        
        $this->assertCount(1, $tachesTerminees);
        $this->assertContains($tache2, $tachesTerminees);
    }

    public function testCollaborateurRepositoryFindByEmail(): void
    {
        // Arrange
        $collaborateur1 = $this->createTestCollaborateur('jean.dupont@example.com');
        $collaborateur2 = $this->createTestCollaborateur('marie.martin@example.com');

        // Act
        $found = $this->collaborateurRepository->findOneBy(['email' => 'jean.dupont@example.com']);

        // Assert
        $this->assertNotNull($found);
        $this->assertEquals($collaborateur1->getId(), $found->getId());
        $this->assertEquals('jean.dupont@example.com', $found->getEmail());
    }

    public function testCollaborateurRepositoryFindByRole(): void
    {
        // Arrange
        $collaborateur1 = $this->createTestCollaborateur('jean@example.com', 'Collaborateur');
        $collaborateur2 = $this->createTestCollaborateur('marie@example.com', 'Manager');
        $collaborateur3 = $this->createTestCollaborateur('pierre@example.com', 'Collaborateur');

        // Act
        $collaborateurs = $this->collaborateurRepository->findBy(['role' => 'Collaborateur']);

        // Assert - Vérifier que nos collaborateurs spécifiques sont présents
        $this->assertGreaterThanOrEqual(2, count($collaborateurs));
        $this->assertContains($collaborateur1, $collaborateurs);
        $this->assertContains($collaborateur3, $collaborateurs);
        $this->assertNotContains($collaborateur2, $collaborateurs);
    }

    public function testCollaborateurRepositoryFindByDisponibilite(): void
    {
        // Arrange
        $collaborateur1 = $this->createTestCollaborateur('dispo@example.com');
        $collaborateur1->setDisponible(true);
        
        $collaborateur2 = $this->createTestCollaborateur('indispo@example.com');
        $collaborateur2->setDisponible(false);

        $this->collaborateurRepository->getEntityManager()->flush();

        // Act
        $disponibles = $this->collaborateurRepository->findBy(['disponible' => true]);
        $indisponibles = $this->collaborateurRepository->findBy(['disponible' => false]);

        // Assert
        $this->assertContains($collaborateur1, $disponibles);
        $this->assertContains($collaborateur2, $indisponibles);
    }

    public function testCompetenceRepositoryFindByNom(): void
    {
        // Arrange
        $competence1 = $this->createTestCompetence('PHP');
        $competence2 = $this->createTestCompetence('JavaScript');

        // Act
        $found = $this->competenceRepository->findOneBy(['nom' => 'PHP']);

        // Assert
        $this->assertNotNull($found);
        $this->assertEquals($competence1->getId(), $found->getId());
        $this->assertEquals('PHP', $found->getNom());
    }

    public function testMissionRepositoryFindByStatut(): void
{
    // Arrange
    $mission1 = $this->createTestMission('Mission en cours', 'en_cours');
    $mission2 = $this->createTestMission('Mission terminée', 'terminee');
    $mission3 = $this->createTestMission('Mission planifiée', 'planifiee');

    // Act
    $missionsEnCours = $this->missionRepository->findBy(['statut' => 'en_cours']);

    // Assert
    $this->assertGreaterThanOrEqual(1, count($missionsEnCours));
    $this->assertContains($mission1, $missionsEnCours);
}


    public function testRepositoryPersistence(): void
    {
        // Test que les entités sont correctement persistées
        $collaborateur = $this->createTestCollaborateur('persistence@example.com');
        $mission = $this->createTestMission('Mission Persistence');
        $competence = $this->createTestCompetence('Persistence');
        $tache = $this->createTestTache('Tâche Persistence', $mission, $collaborateur);

        // Vérifier que les entités ont des IDs (persistées)
        $this->assertNotNull($collaborateur->getId());
        $this->assertNotNull($mission->getId());
        $this->assertNotNull($competence->getId());
        $this->assertNotNull($tache->getId());

        // Vérifier les relations
        $this->assertEquals($mission->getId(), $tache->getMission()->getId());
        $this->assertEquals($collaborateur->getId(), $tache->getCollaborateur()->getId());
    }

    public function testRepositoryUpdate(): void
    {
        // Arrange
        $collaborateur = $this->createTestCollaborateur('update@example.com');
        $originalName = $collaborateur->getNom();

        // Act
        $collaborateur->setNom('Nom Modifié');
        $this->collaborateurRepository->getEntityManager()->flush();

        // Assert
        $updated = $this->collaborateurRepository->find($collaborateur->getId());
        $this->assertEquals('Nom Modifié', $updated->getNom());
        $this->assertNotEquals($originalName, $updated->getNom());
    }

    public function testRepositoryDelete(): void
    {
        // Arrange
        $collaborateur = $this->createTestCollaborateur('delete@example.com');
        $collaborateurId = $collaborateur->getId();

        // Act
        $this->collaborateurRepository->getEntityManager()->remove($collaborateur);
        $this->collaborateurRepository->getEntityManager()->flush();

        // Assert
        $deleted = $this->collaborateurRepository->find($collaborateurId);
        $this->assertNull($deleted);
    }

    // Méthodes utilitaires pour créer des entités de test
    private function createTestCollaborateur(string $email = 'test@example.com', string $role = 'Collaborateur'): Collaborateur
    {
        $collaborateur = new Collaborateur();
        $collaborateur->setNom('Test')
                     ->setPrenom('User')
                     ->setEmail($email)
                     ->setRole($role)
                     ->setDisponible(true);

        $this->collaborateurRepository->getEntityManager()->persist($collaborateur);
        $this->collaborateurRepository->getEntityManager()->flush();

        return $collaborateur;
    }

    private function createTestMission(string $nom = 'Mission Test', string $statut = 'en_cours'): Mission
    {
        // Créer un collaborateur responsable par défaut avec un email unique
        $uniqueEmail = 'responsable_' . uniqid() . '@test.com';
        $responsable = $this->createTestCollaborateur($uniqueEmail, 'Manager');
        
        $mission = new Mission();
        $mission->setTitre($nom)
               ->setDescription('Description de la mission de test')
               ->setPriorite(1)
               ->setDateDebut(new \DateTime())
               ->setDateFinPrevue(new \DateTime('+1 month'))
               ->setStatut($statut)
               ->setResponsable($responsable);

        $this->missionRepository->getEntityManager()->persist($mission);
        $this->missionRepository->getEntityManager()->flush();

        return $mission;
    }

    private function createTestCompetence(string $nom = 'Test'): Competence
    {
        $competence = new Competence();
        $competence->setNom($nom)
                  ->setDescription('Description de la compétence de test');

        $this->competenceRepository->getEntityManager()->persist($competence);
        $this->competenceRepository->getEntityManager()->flush();

        return $competence;
    }

    private function createTestTache(string $titre = 'Tâche Test', ?Mission $mission = null, ?Collaborateur $collaborateur = null, string $statut = 'planifiee'): Tache
    {
        // Créer une mission par défaut si aucune n'est fournie
        if ($mission === null) {
            $mission = $this->createTestMission('Mission par défaut pour ' . $titre);
        }
        
        $tache = new Tache();
        $tache->setTitre($titre)
              ->setDescription('Description de la tâche de test')
              ->setChargeEstimee(10.0)
              ->setDateDebut(new \DateTime())
              ->setDateFinPrevue(new \DateTime('+1 week'))
              ->setStatut($statut)
              ->setPriorite(3)
              ->setMission($mission);

        if ($collaborateur) {
            $tache->setCollaborateur($collaborateur);
        }

        $this->tacheRepository->getEntityManager()->persist($tache);
        $this->tacheRepository->getEntityManager()->flush();

        return $tache;
    }


}
