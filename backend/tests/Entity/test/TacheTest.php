<?php

namespace App\Tests\Entity;

use App\Entity\Tache;
use App\Entity\Mission;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use PHPUnit\Framework\TestCase;

class TacheTest extends TestCase
{
    public function testTacheCreation()
    {
        $tache = new Tache();
        
        $this->assertInstanceOf(Tache::class, $tache);
        $this->assertNull($tache->getId());
    }

    public function testTitreSetterAndGetter()
    {
        $tache = new Tache();
        $titre = 'Développer la page d\'accueil';
        
        $tache->setTitre($titre);
        
        $this->assertEquals($titre, $tache->getTitre());
    }

    public function testDescriptionSetterAndGetter()
    {
        $tache = new Tache();
        $description = 'Créer une page d\'accueil responsive avec Vue.js';
        
        $tache->setDescription($description);
        
        $this->assertEquals($description, $tache->getDescription());
    }

    public function testChargeEstimeeSetterAndGetter()
    {
        $tache = new Tache();
        $charge = 8.5;
        
        $tache->setChargeEstimee($charge);
        
        $this->assertEquals($charge, $tache->getChargeEstimee());
    }

    public function testChargeReelleSetterAndGetter()
    {
        $tache = new Tache();
        $charge = 10.0;
        
        $tache->setChargeReelle($charge);
        
        $this->assertEquals($charge, $tache->getChargeReelle());
    }

    public function testChargeReelleNullable()
    {
        $tache = new Tache();
        
        $this->assertNull($tache->getChargeReelle());
        
        $tache->setChargeReelle(null);
        
        $this->assertNull($tache->getChargeReelle());
    }

    public function testDateDebutSetterAndGetter()
    {
        $tache = new Tache();
        $dateDebut = new \DateTime('2024-01-15');
        
        $tache->setDateDebut($dateDebut);
        
        $this->assertEquals($dateDebut, $tache->getDateDebut());
    }

    public function testDateFinPrevueSetterAndGetter()
    {
        $tache = new Tache();
        $dateFinPrevue = new \DateTime('2024-01-20');
        
        $tache->setDateFinPrevue($dateFinPrevue);
        
        $this->assertEquals($dateFinPrevue, $tache->getDateFinPrevue());
    }

    public function testStatutSetterAndGetter()
    {
        $tache = new Tache();
        $statut = 'en_cours';
        
        $tache->setStatut($statut);
        
        $this->assertEquals($statut, $tache->getStatut());
    }

    public function testPrioriteSetterAndGetter()
    {
        $tache = new Tache();
        $priorite = 2;
        
        $tache->setPriorite($priorite);
        
        $this->assertEquals($priorite, $tache->getPriorite());
    }

    public function testMissionSetterAndGetter()
    {
        $tache = new Tache();
        $mission = new Mission();
        $mission->setTitre('Projet Web');
        
        $tache->setMission($mission);
        
        $this->assertEquals($mission, $tache->getMission());
    }

    public function testCollaborateurSetterAndGetter()
    {
        $tache = new Tache();
        $collaborateur = new Collaborateur();
        $collaborateur->setNom('Dupont');
        $collaborateur->setPrenom('Jean');
        
        $tache->setCollaborateur($collaborateur);
        
        $this->assertEquals($collaborateur, $tache->getCollaborateur());
    }

    public function testCollaborateurNullable()
    {
        $tache = new Tache();
        
        $this->assertNull($tache->getCollaborateur());
        
        $tache->setCollaborateur(null);
        
        $this->assertNull($tache->getCollaborateur());
    }

    public function testCompetenceRequiseSetterAndGetter()
    {
        $tache = new Tache();
        $competence = new Competence();
        $competence->setNom('Vue.js');
        
        $tache->setCompetenceRequise($competence);
        
        $this->assertEquals($competence, $tache->getCompetenceRequise());
    }

    public function testCompetenceRequiseNullable()
    {
        $tache = new Tache();
        
        $this->assertNull($tache->getCompetenceRequise());
        
        $tache->setCompetenceRequise(null);
        
        $this->assertNull($tache->getCompetenceRequise());
    }

    public function testFluentInterface()
    {
        $tache = new Tache();
        $mission = new Mission();
        $collaborateur = new Collaborateur();
        $competence = new Competence();
        $dateDebut = new \DateTime('2024-01-15');
        $dateFin = new \DateTime('2024-01-20');
        
        $result = $tache
            ->setTitre('Tâche Test')
            ->setDescription('Description de test')
            ->setChargeEstimee(5.0)
            ->setChargeReelle(6.0)
            ->setDateDebut($dateDebut)
            ->setDateFinPrevue($dateFin)
            ->setStatut('en_cours')
            ->setPriorite(1)
            ->setMission($mission)
            ->setCollaborateur($collaborateur)
            ->setCompetenceRequise($competence);
        
        $this->assertSame($tache, $result);
        $this->assertEquals('Tâche Test', $tache->getTitre());
        $this->assertEquals('Description de test', $tache->getDescription());
        $this->assertEquals(5.0, $tache->getChargeEstimee());
        $this->assertEquals(6.0, $tache->getChargeReelle());
        $this->assertEquals($dateDebut, $tache->getDateDebut());
        $this->assertEquals($dateFin, $tache->getDateFinPrevue());
        $this->assertEquals('en_cours', $tache->getStatut());
        $this->assertEquals(1, $tache->getPriorite());
        $this->assertEquals($mission, $tache->getMission());
        $this->assertEquals($collaborateur, $tache->getCollaborateur());
        $this->assertEquals($competence, $tache->getCompetenceRequise());
    }

    public function testCompleteTacheSetup()
    {
        $tache = new Tache();
        $mission = new Mission();
        $mission->setTitre('Projet E-commerce');
        
        $collaborateur = new Collaborateur();
        $collaborateur->setNom('Martin');
        $collaborateur->setPrenom('Sophie');
        
        $competence = new Competence();
        $competence->setNom('React');
        
        $tache->setTitre('Créer le panier')
            ->setDescription('Implémenter la fonctionnalité de panier d\'achat')
            ->setChargeEstimee(12.0)
            ->setDateDebut(new \DateTime('2024-02-01'))
            ->setDateFinPrevue(new \DateTime('2024-02-15'))
            ->setStatut('planifiée')
            ->setPriorite(2)
            ->setMission($mission)
            ->setCollaborateur($collaborateur)
            ->setCompetenceRequise($competence);
        
        $this->assertEquals('Créer le panier', $tache->getTitre());
        $this->assertEquals('Implémenter la fonctionnalité de panier d\'achat', $tache->getDescription());
        $this->assertEquals(12.0, $tache->getChargeEstimee());
        $this->assertEquals('planifiée', $tache->getStatut());
        $this->assertEquals(2, $tache->getPriorite());
        $this->assertEquals($mission, $tache->getMission());
        $this->assertEquals($collaborateur, $tache->getCollaborateur());
        $this->assertEquals($competence, $tache->getCompetenceRequise());
    }
}
