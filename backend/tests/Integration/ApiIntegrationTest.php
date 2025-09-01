<?php

namespace App\Tests\Integration;

use App\Tests\ApiTestBase;
use App\Entity\User;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use App\Entity\Mission;
use App\Entity\Tache;
use Doctrine\ORM\EntityManagerInterface;

class ApiIntegrationTest extends ApiTestBase
{
    private EntityManagerInterface $entityManager;
    private string $adminToken;
    private string $userToken;

    protected function setUp(): void
    {
        parent::setUp();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        
        // Créer des utilisateurs de test
        $this->createTestUsers();
        
        // Obtenir les tokens d'authentification
        $this->adminToken = $this->jwt('admin@example.com');
        $this->userToken = $this->jwt('user@example.com');
    }

    public function testCompleteTacheWorkflow(): void
    {
        // 1. Créer une compétence
        $competence = $this->createTestCompetence();
        
        // 2. Créer une mission
        $mission = $this->createTestMission();
        
        // 3. Créer un collaborateur avec la compétence
        $collaborateur = $this->createTestCollaborateur();
        $this->assignCompetenceToCollaborateur($collaborateur, $competence);
        
        // 4. Créer une tâche qui sera automatiquement assignée
        $tacheData = [
            'titre' => 'Tâche de test intégration',
            'description' => 'Description de la tâche de test',
            'chargeEstimee' => 15.0,
            'dateDebut' => '2024-01-01',
            'dateFinPrevue' => '2024-01-15',
            'statut' => 'planifiée',
            'mission' => $mission->getId(),
            'competenceRequise' => $competence->getId(),
            'priorite' => 2
        ];

        $response = static::getClient()->request('POST', '/api/taches', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $tacheData
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        
        $responseData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertArrayHasKey('collaborateurAssigne', $responseData);
        $this->assertEquals($collaborateur->getId(), $responseData['collaborateurAssigne']['id']);

        // 5. Vérifier que la tâche a été créée et assignée
        $tacheId = $responseData['data']['id'];
        $response = static::getClient()->request('GET', "/api/taches/{$tacheId}", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);

        $this->assertResponseIsSuccessful();
        $tacheData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertEquals($tacheId, $tacheData['data']['id']);
        $this->assertEquals($collaborateur->getId(), $tacheData['data']['collaborateur']['id']);

        // 6. Mettre à jour la tâche
        $updateData = [
            'statut' => 'en_cours',
            'chargeReelle' => 12.0
        ];

        $response = static::getClient()->request('PUT', "/api/taches/{$tacheId}", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $updateData
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertEquals('en_cours', $responseData['data']['statut']);
        $this->assertEquals(12.0, $responseData['data']['chargeReelle']);

        // 7. Récupérer les tâches du collaborateur
        $response = static::getClient()->request('GET', "/api/taches/collaborateur/email/{$collaborateur->getEmail()}", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertCount(1, $responseData['data']);
        $this->assertEquals($tacheId, $responseData['data'][0]['id']);

        // 8. Récupérer les statistiques de charge du collaborateur
        $response = static::getClient()->request('GET', "/api/collaborateurs/{$collaborateur->getId()}/workload-stats", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertArrayHasKey('chargeActuelle', $responseData['data']);
        $this->assertArrayHasKey('pourcentageCharge', $responseData['data']);

        // 9. Supprimer la tâche
        $response = static::getClient()->request('DELETE', "/api/taches/{$tacheId}", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testCollaborateurWorkflow(): void
    {
        // 1. Créer un collaborateur
        $collaborateurData = [
            'prenom' => 'Jean',
            'nom' => 'Dupont',
            'email' => 'jean.dupont.integration@example.com',
            'role' => 'Collaborateur',
            'disponible' => true
        ];

        $response = static::getClient()->request('POST', '/api/collaborateurs', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $collaborateurData
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(201);
        
        $responseData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertArrayHasKey('userAccount', $responseData);
        $this->assertEquals('jean.dupont.integration@example.com', $responseData['userAccount']['email']);
        
        $collaborateurId = $responseData['data']['id'];

        // 2. Récupérer le collaborateur
        $response = static::getClient()->request('GET', "/api/collaborateurs/{$collaborateurId}", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertEquals('Jean', $responseData['data']['prenom']);
        $this->assertEquals('Dupont', $responseData['data']['nom']);

        // 3. Mettre à jour le collaborateur
        $updateData = [
            'prenom' => 'Jean-Pierre',
            'role' => 'Manager'
        ];

        $response = static::getClient()->request('PUT', "/api/collaborateurs/{$collaborateurId}", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $updateData
        ]);

        $this->assertResponseIsSuccessful();
        $responseData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertEquals('Jean-Pierre', $responseData['data']['prenom']);
        $this->assertEquals('Manager', $responseData['data']['role']);

        // 4. Mettre à jour la disponibilité
        $response = static::getClient()->request('POST', "/api/collaborateurs/{$collaborateurId}/update-availability", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);

        $this->assertResponseIsSuccessful();

        // 5. Supprimer le collaborateur
        $response = static::getClient()->request('DELETE', "/api/collaborateurs/{$collaborateurId}", [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);

        $this->assertResponseStatusCodeSame(204);
    }

    public function testAuthenticationAndAuthorization(): void
    {
        // Test sans authentification
        static::getClient()->request('GET', '/api/taches');
        $this->assertResponseStatusCodeSame(401);

        // Test avec token invalide
        static::getClient()->request('GET', '/api/taches', [
            'headers' => ['Authorization' => 'Bearer invalid_token']
        ]);
        $this->assertResponseStatusCodeSame(401);

        // Test avec token valide
        static::getClient()->request('GET', '/api/taches', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);
        $this->assertResponseIsSuccessful();
    }

    public function testErrorHandling(): void
    {
        // Test création de tâche avec données invalides
        $invalidData = [
            'titre' => 'Tâche sans données complètes'
            // Manque des champs obligatoires
        ];

        $response = static::getClient()->request('POST', '/api/taches', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken],
            'json' => $invalidData
        ]);

        $this->assertResponseStatusCodeSame(422);
        $responseData = json_decode(static::getClient()->getResponse()->getContent(), true);
        $this->assertStringContainsString('Champ obligatoire manquant', $responseData['message']);

        // Test récupération de ressource inexistante
        $response = static::getClient()->request('GET', '/api/taches/99999', [
            'headers' => ['Authorization' => 'Bearer ' . $this->adminToken]
        ]);

        $this->assertResponseStatusCodeSame(404);
    }

    private function createTestUsers(): void
    {
        // Créer un utilisateur admin
        $admin = new User();
        $admin->setEmail('admin@example.com');
        $admin->setPassword('$2y$13$hashed_password'); // Mot de passe hashé
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setIsActive(true);
        $admin->setMustChangePassword(false);
        
        $this->entityManager->persist($admin);

        // Créer un utilisateur normal
        $user = new User();
        $user->setEmail('user@example.com');
        $user->setPassword('$2y$13$hashed_password'); // Mot de passe hashé
        $user->setRoles(['ROLE_USER']);
        $user->setIsActive(true);
        $user->setMustChangePassword(false);
        
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    private function createTestCompetence(): Competence
    {
        $competence = new Competence();
        $competence->setNom('PHP');
        $competence->setDescription('Compétence en PHP');
        
        $this->entityManager->persist($competence);
        $this->entityManager->flush();
        
        return $competence;
    }

    private function createTestMission(): Mission
    {
        $mission = new Mission();
        $mission->setTitre('Mission de test');
        $mission->setDescription('Description de la mission de test');
        $mission->setDateDebut(new \DateTime());
        $mission->setDateFinPrevue(new \DateTime('+1 month'));
        $mission->setStatut('en_cours');
        
        $this->entityManager->persist($mission);
        $this->entityManager->flush();
        
        return $mission;
    }

    private function createTestCollaborateur(): Collaborateur
    {
        $collaborateur = new Collaborateur();
        $collaborateur->setPrenom('Test');
        $collaborateur->setNom('Collaborateur');
        $collaborateur->setEmail('test.collaborateur@example.com');
        $collaborateur->setRole('Collaborateur');
        $collaborateur->setDisponible(true);
        
        $this->entityManager->persist($collaborateur);
        $this->entityManager->flush();
        
        return $collaborateur;
    }

    private function assignCompetenceToCollaborateur(Collaborateur $collaborateur, Competence $competence): void
    {
        $collabComp = new \App\Entity\CollaborateurCompetence();
        $collabComp->setCollaborateur($collaborateur);
        $collabComp->setCompetence($competence);
        $collabComp->setNiveau(8);
        
        $collaborateur->addCompetence($collabComp);
        
        $this->entityManager->persist($collabComp);
        $this->entityManager->flush();
    }
}