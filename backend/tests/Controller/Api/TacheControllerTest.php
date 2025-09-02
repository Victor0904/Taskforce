<?php

namespace App\Tests\Controller\Api;

use App\Controller\Api\TacheController;
use App\Entity\Tache;
use App\Entity\Mission;
use App\Entity\Competence;
use App\Entity\Collaborateur;
use App\Repository\TacheRepository;
use App\Service\TaskAssignmentService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class TacheControllerTest extends TestCase
{
    private TacheController $controller;
    private MockObject $taskAssignmentService;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|TacheRepository $tacheRepository;
    private MockObject|EntityRepository $missionRepository;
    private MockObject|EntityRepository $competenceRepository;
    private MockObject|EntityRepository $collaborateurRepository;

    protected function setUp(): void
    {
        $this->taskAssignmentService = $this->createMock(TaskAssignmentService::class);
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->tacheRepository = $this->createMock(TacheRepository::class);
        $this->missionRepository = $this->createMock(EntityRepository::class);
        $this->competenceRepository = $this->createMock(EntityRepository::class);
        $this->collaborateurRepository = $this->createMock(EntityRepository::class);

        $this->controller = new TacheController($this->taskAssignmentService);
    }

    public function testIndexReturnsAllTaches(): void
    {
        // Arrange
        $competence = $this->createCompetence(1, 'PHP');
        $taches = [
            $this->createTache(1, 'Tâche 1', null, $competence),
            $this->createTache(2, 'Tâche 2', null, $competence)
        ];

        $this->tacheRepository->expects($this->once())
            ->method('findAll')
            ->willReturn($taches);

        // Act
        $response = $this->controller->index($this->tacheRepository);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Liste des tâches.', $data['message']);
        $this->assertCount(2, $data['data']);
    }

    public function testShowReturnsSpecificTache(): void
    {
        // Arrange
        $competence = $this->createCompetence(1, 'PHP');
        $tache = $this->createTache(1, 'Tâche test', null, $competence);

        // Act
        $response = $this->controller->show($tache);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Tâche trouvée.', $data['message']);
        $this->assertIsArray($data['data']);
    }

    public function testGetByProjetReturnsTachesForProject(): void
    {
        // Arrange
        $projectId = 1;
        $competence = $this->createCompetence(1, 'PHP');
        $taches = [
            $this->createTache(1, 'Tâche projet 1', null, $competence),
            $this->createTache(2, 'Tâche projet 2', null, $competence)
        ];

        $this->tacheRepository->expects($this->once())
            ->method('findBy')
            ->with(['mission' => $projectId])
            ->willReturn($taches);

        // Act
        $response = $this->controller->getByProjet($projectId, $this->tacheRepository);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertCount(2, $data);
    }

    public function testGetByCollaborateurEmailReturnsTachesForCollaborateur(): void
    {
        // Arrange
        $email = 'jean.dupont@example.com';
        $collaborateur = $this->createCollaborateur(1, 'Jean', 'Dupont', $email);
        $competence = $this->createCompetence(1, 'PHP');
        $taches = [
            $this->createTache(1, 'Tâche 1', $collaborateur, $competence),
            $this->createTache(2, 'Tâche 2', $collaborateur, $competence)
        ];

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnMap([
                [Collaborateur::class, $this->collaborateurRepository],
                [Tache::class, $this->tacheRepository]
            ]);

        $this->collaborateurRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn($collaborateur);

        $this->tacheRepository->expects($this->once())
            ->method('findBy')
            ->with(['collaborateur' => $collaborateur])
            ->willReturn($taches);

        // Act
        $response = $this->controller->getByCollaborateurEmail($email, $this->entityManager);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Tâches du collaborateur récupérées.', $data['message']);
        $this->assertCount(2, $data['data']);
    }

    public function testGetByCollaborateurEmailReturns404WhenCollaborateurNotFound(): void
    {
        // Arrange
        $email = 'inexistant@example.com';

        $this->entityManager->expects($this->once())
            ->method('getRepository')
            ->with(Collaborateur::class)
            ->willReturn($this->collaborateurRepository);

        $this->collaborateurRepository->expects($this->once())
            ->method('findOneBy')
            ->with(['email' => $email])
            ->willReturn(null);

        // Act
        $response = $this->controller->getByCollaborateurEmail($email, $this->entityManager);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Collaborateur non trouvé', $data['message']);
    }

    public function testCreateSuccessfullyCreatesAndAssignsTache(): void
    {
        // Arrange
        $requestData = [
            'titre' => 'Nouvelle tâche',
            'description' => 'Description de la tâche',
            'chargeEstimee' => 10.0,
            'dateDebut' => '2024-01-01',
            'dateFinPrevue' => '2024-01-15',
            'statut' => 'planifiée',
            'mission' => 1,
            'competenceRequise' => 1,
            'priorite' => 3
        ];

        $request = new Request([], [], [], [], [], [], json_encode($requestData));

        $mission = $this->createMission(1, 'Mission test');
        $competence = $this->createCompetence(1, 'PHP');
        $collaborateur = $this->createCollaborateur(1, 'Jean', 'Dupont');

        // Mock des repositories
        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnMap([
                [Mission::class, $this->missionRepository],
                [Competence::class, $this->competenceRepository]
            ]);

        $this->missionRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($mission);

        $this->competenceRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($competence);

        // Mock de la persistance
        $this->entityManager->expects($this->once())
            ->method('persist');
        $this->entityManager->expects($this->once())
            ->method('flush');

        // Mock de l'assignation automatique
        $this->taskAssignmentService->expects($this->once())
            ->method('assignTaskAutomatically')
            ->willReturn($collaborateur);

        // Act
        $response = $this->controller->create($request, $this->entityManager);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('Tâche créée et assignée automatiquement à Jean Dupont', $data['message']);
        $this->assertArrayHasKey('collaborateurAssigne', $data);
    }

    public function testCreateReturns422WhenRequiredFieldMissing(): void
    {
        // Arrange
        $requestData = [
            'titre' => 'Nouvelle tâche',
            'description' => 'Description de la tâche',
            // 'chargeEstimee' manquant
            'dateDebut' => '2024-01-01',
            'dateFinPrevue' => '2024-01-15',
            'statut' => 'planifiée',
            'mission' => 1,
            'competenceRequise' => 1,
            'priorite' => 3
        ];

        $request = new Request([], [], [], [], [], [], json_encode($requestData));

        // Act
        $response = $this->controller->create($request, $this->entityManager);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('Champ obligatoire manquant : chargeEstimee', $data['message']);
    }

    public function testCreateReturns404WhenMissionNotFound(): void
    {
        // Arrange
        $requestData = [
            'titre' => 'Nouvelle tâche',
            'description' => 'Description de la tâche',
            'chargeEstimee' => 10.0,
            'dateDebut' => '2024-01-01',
            'dateFinPrevue' => '2024-01-15',
            'statut' => 'planifiée',
            'mission' => 999, // Mission inexistante
            'competenceRequise' => 1,
            'priorite' => 3
        ];

        $request = new Request([], [], [], [], [], [], json_encode($requestData));

        $this->entityManager->expects($this->exactly(2))
            ->method('getRepository')
            ->willReturnMap([
                [Mission::class, $this->missionRepository],
                [Competence::class, $this->competenceRepository]
            ]);

        $this->missionRepository->expects($this->once())
            ->method('find')
            ->with(999)
            ->willReturn(null);

        $this->competenceRepository->expects($this->once())
            ->method('find')
            ->with(1)
            ->willReturn($this->createCompetence(1, 'PHP'));

        // Act
        $response = $this->controller->create($request, $this->entityManager);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(404, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Mission ou compétence introuvable.', $data['message']);
    }

    public function testDeleteSuccessfullyDeletesTache(): void
    {
        // Arrange
        $collaborateur = $this->createCollaborateur(1, 'Jean', 'Dupont');
        $competence = $this->createCompetence(1, 'PHP');
        $tache = $this->createTache(1, 'Tâche à supprimer', $collaborateur, $competence);

        $this->entityManager->expects($this->once())
            ->method('remove')
            ->with($tache);
        $this->entityManager->expects($this->once())
            ->method('flush');

        $this->taskAssignmentService->expects($this->once())
            ->method('updateCollaborateurAvailability')
            ->with($collaborateur);

        // Act
        $response = $this->controller->delete($tache, $this->entityManager);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(204, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Tâche supprimée avec succès.', $data['message']);
    }

    public function testReassignTaskSuccessfullyReassignsTache(): void
    {
        // Arrange
        $competence = $this->createCompetence(1, 'PHP');
        $ancienCollaborateur = $this->createCollaborateur(1, 'Jean', 'Dupont');
        $nouveauCollaborateur = $this->createCollaborateur(2, 'Marie', 'Martin');
        
        $tache = $this->createTache(1, 'Tâche à réassigner', $ancienCollaborateur, $competence);

        $this->taskAssignmentService->expects($this->once())
            ->method('assignTaskAutomatically')
            ->with($tache)
            ->willReturn($nouveauCollaborateur);

        $this->taskAssignmentService->expects($this->exactly(2))
            ->method('updateCollaborateurAvailability')
            ->withConsecutive([$ancienCollaborateur], [$nouveauCollaborateur]);

        // Act
        $response = $this->controller->reassignTask($tache, $this->entityManager);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertStringContainsString('Tâche réassignée à Marie Martin', $data['message']);
        $this->assertArrayHasKey('nouveauCollaborateur', $data);
    }

    public function testReassignTaskReturns422WhenNoCompetenceRequired(): void
    {
        // Arrange
        $tache = $this->createTache(1, 'Tâche sans compétence');
        // Pas de compétence requise définie (null par défaut)

        // Act
        $response = $this->controller->reassignTask($tache, $this->entityManager);

        // Assert
        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(422, $response->getStatusCode());
        
        $data = json_decode($response->getContent(), true);
        $this->assertEquals('Impossible de réassigner une tâche sans compétence requise', $data['message']);
    }

    // Méthodes utilitaires pour créer des objets de test
    private function createTache(int $id, string $titre, ?Collaborateur $collaborateur = null, ?Competence $competence = null): Tache
    {
        $tache = new Tache();
        // Note: setId n'existe pas, l'ID est généré automatiquement par Doctrine
        $tache->setTitre($titre);
        $tache->setDescription('Description de test');
        $tache->setChargeEstimee(10.0);
        $tache->setDateDebut(new \DateTime());
        $tache->setDateFinPrevue(new \DateTime('+1 week'));
        $tache->setStatut('planifiée');
        $tache->setPriorite(3);
        
        if ($collaborateur) {
            $tache->setCollaborateur($collaborateur);
        }
        
        if ($competence) {
            $tache->setCompetenceRequise($competence);
        }
        
        return $tache;
    }

    private function createMission(int $id, string $nom): Mission
    {
        $mission = new Mission();
        // Note: setId n'existe pas, l'ID est généré automatiquement par Doctrine
        $mission->setTitre($nom);
        $mission->setDescription('Description de mission');
        $mission->setDateDebut(new \DateTime());
        $mission->setDateFinPrevue(new \DateTime('+1 month'));
        $mission->setStatut('en_cours');
        
        return $mission;
    }

    private function createCompetence(int $id, string $nom): Competence
    {
        $competence = new Competence();
        // Note: setId n'existe pas, l'ID est généré automatiquement par Doctrine
        $competence->setNom($nom);
        $competence->setDescription('Description de compétence');
        
        return $competence;
    }

    private function createCollaborateur(int $id, string $prenom, string $nom, string $email = null): Collaborateur
    {
        $collaborateur = new Collaborateur();
        // Note: setId n'existe pas, l'ID est généré automatiquement par Doctrine
        $collaborateur->setPrenom($prenom);
        $collaborateur->setNom($nom);
        $collaborateur->setEmail($email ?? strtolower($prenom . '.' . $nom . '@example.com'));
        $collaborateur->setRole('Collaborateur');
        $collaborateur->setDisponible(true);
        
        return $collaborateur;
    }
}
