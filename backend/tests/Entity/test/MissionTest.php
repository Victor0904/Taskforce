<?php

namespace App\Tests\Entity;

use App\Entity\Mission;
use App\Entity\Collaborateur;
use App\Entity\Tache;
use PHPUnit\Framework\TestCase;

class MissionTest extends TestCase
{
    public function testMissionCreation()
    {
        $mission = new Mission();
        
        $this->assertInstanceOf(Mission::class, $mission);
        $this->assertNull($mission->getId());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $mission->getTaches());
        $this->assertCount(0, $mission->getTaches());
    }

    public function testTitreSetterAndGetter()
    {
        $mission = new Mission();
        $titre = 'Projet Web';
        
        $mission->setTitre($titre);
        
        $this->assertEquals($titre, $mission->getTitre());
    }

    public function testDescriptionSetterAndGetter()
    {
        $mission = new Mission();
        $description = 'Développement d\'une application web moderne';
        
        $mission->setDescription($description);
        
        $this->assertEquals($description, $mission->getDescription());
    }

    public function testPrioriteSetterAndGetter()
    {
        $mission = new Mission();
        $priorite = 3;
        
        $mission->setPriorite($priorite);
        
        $this->assertEquals($priorite, $mission->getPriorite());
    }

    public function testDateDebutSetterAndGetter()
    {
        $mission = new Mission();
        $dateDebut = new \DateTime('2024-01-01');
        
        $mission->setDateDebut($dateDebut);
        
        $this->assertEquals($dateDebut, $mission->getDateDebut());
    }

    public function testDateFinPrevueSetterAndGetter()
    {
        $mission = new Mission();
        $dateFinPrevue = new \DateTime('2024-12-31');
        
        $mission->setDateFinPrevue($dateFinPrevue);
        
        $this->assertEquals($dateFinPrevue, $mission->getDateFinPrevue());
    }

    public function testStatutSetterAndGetter()
    {
        $mission = new Mission();
        $statut = 'en_cours';
        
        $mission->setStatut($statut);
        
        $this->assertEquals($statut, $mission->getStatut());
    }

    public function testResponsableSetterAndGetter()
    {
        $mission = new Mission();
        $responsable = new Collaborateur();
        $responsable->setNom('Dupont');
        $responsable->setPrenom('Jean');
        
        $mission->setResponsable($responsable);
        
        $this->assertEquals($responsable, $mission->getResponsable());
    }

    public function testTachesManagement()
    {
        $mission = new Mission();
        $tache = new Tache();
        $tache->setTitre('Tâche 1');
        $tache->setDescription('Description de la tâche');
        
        // Test ajout de tâche
        $mission->addTache($tache);
        
        $this->assertCount(1, $mission->getTaches());
        $this->assertTrue($mission->getTaches()->contains($tache));
        $this->assertEquals($mission, $tache->getMission());
        
        // Test suppression de tâche
        $mission->removeTache($tache);
        
        $this->assertCount(0, $mission->getTaches());
        $this->assertFalse($mission->getTaches()->contains($tache));
    }

    public function testMultipleTaches()
    {
        $mission = new Mission();
        
        $tache1 = new Tache();
        $tache1->setTitre('Tâche 1');
        
        $tache2 = new Tache();
        $tache2->setTitre('Tâche 2');
        
        $mission->addTache($tache1);
        $mission->addTache($tache2);
        
        $this->assertCount(2, $mission->getTaches());
    }

    public function testFluentInterface()
    {
        $mission = new Mission();
        $responsable = new Collaborateur();
        $dateDebut = new \DateTime('2024-01-01');
        $dateFin = new \DateTime('2024-12-31');
        
        $result = $mission
            ->setTitre('Projet Test')
            ->setDescription('Description du projet')
            ->setPriorite(2)
            ->setDateDebut($dateDebut)
            ->setDateFinPrevue($dateFin)
            ->setStatut('en_cours')
            ->setResponsable($responsable);
        
        $this->assertSame($mission, $result);
        $this->assertEquals('Projet Test', $mission->getTitre());
        $this->assertEquals('Description du projet', $mission->getDescription());
        $this->assertEquals(2, $mission->getPriorite());
        $this->assertEquals($dateDebut, $mission->getDateDebut());
        $this->assertEquals($dateFin, $mission->getDateFinPrevue());
        $this->assertEquals('en_cours', $mission->getStatut());
        $this->assertEquals($responsable, $mission->getResponsable());
    }

    public function testTachesCollectionIsEmptyByDefault()
    {
        $mission = new Mission();
        
        $this->assertCount(0, $mission->getTaches());
    }

    public function testAddSameTacheTwice()
    {
        $mission = new Mission();
        $tache = new Tache();
        $tache->setTitre('Tâche unique');
        
        $mission->addTache($tache);
        $mission->addTache($tache); // Ajout du même objet
        
        $this->assertCount(1, $mission->getTaches());
    }
}
