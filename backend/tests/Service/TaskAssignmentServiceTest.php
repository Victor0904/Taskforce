<?php

namespace App\Tests\Service;

use App\Entity\Tache;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use App\Entity\CollaborateurCompetence;
use App\Service\TaskAssignmentService;
use App\Repository\CollaborateurRepository;
use App\Repository\TacheRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Query;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class TaskAssignmentServiceTest extends TestCase
{
    private TaskAssignmentService $service;
    private MockObject|EntityManagerInterface $entityManager;
    private MockObject|CollaborateurRepository $collaborateurRepository;
    private MockObject|TacheRepository $tacheRepository;
    private MockObject|QueryBuilder $queryBuilder;
    private MockObject|Query $query;

    protected function setUp(): void
    {
        $this->entityManager = $this->createMock(EntityManagerInterface::class);
        $this->collaborateurRepository = $this->createMock(CollaborateurRepository::class);
        $this->tacheRepository = $this->createMock(TacheRepository::class);
        $this->queryBuilder = $this->createMock(QueryBuilder::class);
        $this->query = $this->createMock(Query::class);

        $this->service = new TaskAssignmentService(
            $this->entityManager,
            $this->collaborateurRepository,
            $this->tacheRepository
        );
    }

    public function testCanAssignTaskWithAvailableWorkload(): void
    {
        // Arrange
        $tache = new Tache();
        $tache->setChargeEstimee(10.0);

        $collaborateur = new Collaborateur();
        $collaborateur->setPrenom('Jean');
        $collaborateur->setNom('Dupont');

        // Mock pour calculateCurrentWorkload qui retourne 20h
        $this->setupCurrentWorkloadMock($collaborateur, 20.0);

        // Act
        $result = $this->service->canAssignTask($tache, $collaborateur);

        // Assert
        $this->assertTrue($result); // 20h + 10h = 30h < 60h (limite)
    }

    public function testCannotAssignTaskWhenOverloaded(): void
    {
        // Arrange
        $tache = new Tache();
        $tache->setChargeEstimee(50.0); // Tâche très lourde

        $collaborateur = new Collaborateur();
        $collaborateur->setPrenom('Marie');
        $collaborateur->setNom('Martin');

        // Mock pour calculateCurrentWorkload qui retourne 20h
        $this->setupCurrentWorkloadMock($collaborateur, 20.0);

        // Act
        $result = $this->service->canAssignTask($tache, $collaborateur);

        // Assert
        $this->assertFalse($result); // 20h + 50h = 70h > 60h (limite)
    }

    public function testGetWorkloadStatsReturnsCorrectData(): void
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $collaborateur->setPrenom('Pierre');
        $collaborateur->setNom('Durand');

        // Mock pour calculateCurrentWorkload qui retourne 30h
        $this->setupCurrentWorkloadMock($collaborateur, 30.0);

        // Act
        $stats = $this->service->getWorkloadStats($collaborateur);

        // Assert
        $this->assertEquals(30.0, $stats['chargeActuelle']);
        $this->assertEquals(48.0, $stats['chargeMaximale']); // 80% de 60h
        $this->assertEquals(60.0, $stats['chargeMaximaleTotale']);
        $this->assertEquals(62.5, $stats['pourcentageCharge']); // 30/48 * 100
        $this->assertEquals(50.0, $stats['pourcentageChargeTotal']); // 30/60 * 100
        $this->assertTrue($stats['disponible']); // 62.5% < 80%
        $this->assertFalse($stats['surcharge']); // < 60% de 60h
        $this->assertFalse($stats['critique']); // < 80% de 60h
    }

    public function testGetWorkloadStatsWithCriticalWorkload(): void
    {
        // Arrange
        $collaborateur = new Collaborateur();
        $collaborateur->setPrenom('Sophie');
        $collaborateur->setNom('Bernard');

        // Mock pour calculateCurrentWorkload qui retourne 50h (critique)
        $this->setupCurrentWorkloadMock($collaborateur, 50.0);

        // Act
        $stats = $this->service->getWorkloadStats($collaborateur);

        // Assert
        $this->assertEquals(50.0, $stats['chargeActuelle']);
        $this->assertTrue($stats['surcharge']); // > 60% de 60h
        $this->assertTrue($stats['critique']); // > 80% de 60h
    }

    public function testAssignTaskAutomaticallyThrowsExceptionWhenNoCompetence(): void
    {
        // Arrange
        $tache = new Tache();
        $tache->setChargeEstimee(10.0);
        // Pas de compétence requise définie

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Une compétence est requise pour assigner automatiquement une tâche');
        
        $this->service->assignTaskAutomatically($tache);
    }

    public function testAssignTaskAutomaticallyThrowsExceptionWhenNoCompetentCollaborator(): void
    {
        // Arrange
        $competence = new Competence();
        $competence->setNom('PHP');

        $tache = new Tache();
        $tache->setChargeEstimee(10.0);
        $tache->setCompetenceRequise($competence);

        // Mock pour findCollaborateursWithCompetence qui retourne un tableau vide
        $this->setupFindCollaborateursWithCompetenceMock($competence, []);

        // Act & Assert
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Aucun collaborateur n'a la compétence requise: PHP");
        
        $this->service->assignTaskAutomatically($tache);
    }

    public function testAssignTaskAutomaticallySuccessfullyAssignsTask(): void
    {
        // Ce test est trop complexe pour un test unitaire avec des mocks
        // Il serait mieux de le faire en test d'intégration
        $this->markTestSkipped('Test trop complexe pour un test unitaire - à faire en test d\'intégration');
    }

    private function setupCurrentWorkloadMock(Collaborateur $collaborateur, float $workload): void
    {
        $this->entityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder->expects($this->any())
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('andWhere')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query->expects($this->any())
            ->method('getSingleScalarResult')
            ->willReturn($workload);
    }

    private function setupFindCollaborateursWithCompetenceMock(Competence $competence, array $collaborateurs): void
    {
        $this->entityManager->expects($this->any())
            ->method('createQueryBuilder')
            ->willReturn($this->queryBuilder);

        $this->queryBuilder->expects($this->any())
            ->method('select')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('from')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('join')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('where')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('andWhere')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('setParameter')
            ->willReturnSelf();

        $this->queryBuilder->expects($this->any())
            ->method('getQuery')
            ->willReturn($this->query);

        $this->query->expects($this->any())
            ->method('getResult')
            ->willReturn($collaborateurs);
    }
}
