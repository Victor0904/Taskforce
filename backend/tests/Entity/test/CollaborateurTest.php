<?php

namespace App\Tests\Entity;

use App\Entity\Collaborateur;
use App\Entity\CollaborateurCompetence;
use App\Entity\Competence;
use PHPUnit\Framework\TestCase;

class CollaborateurTest extends TestCase
{
    public function testCollaborateurCreation()
    {
        $collaborateur = new Collaborateur();
        
        $this->assertInstanceOf(Collaborateur::class, $collaborateur);
        $this->assertNull($collaborateur->getId());
        $this->assertTrue($collaborateur->isDisponible());
        $this->assertEquals(0, $collaborateur->getChargeActuelle());
        $this->assertInstanceOf(\Doctrine\Common\Collections\Collection::class, $collaborateur->getCompetences());
    }

    public function testNomSetterAndGetter()
    {
        $collaborateur = new Collaborateur();
        $nom = 'Dupont';
        
        $collaborateur->setNom($nom);
        
        $this->assertEquals($nom, $collaborateur->getNom());
    }

    public function testPrenomSetterAndGetter()
    {
        $collaborateur = new Collaborateur();
        $prenom = 'Jean';
        
        $collaborateur->setPrenom($prenom);
        
        $this->assertEquals($prenom, $collaborateur->getPrenom());
    }

    public function testEmailSetterAndGetter()
    {
        $collaborateur = new Collaborateur();
        $email = 'jean.dupont@example.com';
        
        $collaborateur->setEmail($email);
        
        $this->assertEquals($email, $collaborateur->getEmail());
    }

    public function testRoleSetterAndGetter()
    {
        $collaborateur = new Collaborateur();
        $role = 'Manager';
        
        $collaborateur->setRole($role);
        
        $this->assertEquals($role, $collaborateur->getRole());
    }

    public function testChargeActuelleSetterAndGetter()
    {
        $collaborateur = new Collaborateur();
        $charge = 25.5;
        
        $collaborateur->setChargeActuelle($charge);
        
        $this->assertEquals($charge, $collaborateur->getChargeActuelle());
    }

    public function testDisponibleSetterAndGetter()
    {
        $collaborateur = new Collaborateur();
        
        $this->assertTrue($collaborateur->isDisponible());
        
        $collaborateur->setDisponible(false);
        $this->assertFalse($collaborateur->isDisponible());
        
        $collaborateur->setDisponible(true);
        $this->assertTrue($collaborateur->isDisponible());
    }

    public function testCompetencesManagement()
    {
        $collaborateur = new Collaborateur();
        $competence = new Competence();
        $competence->setNom('PHP');
        
        $collabCompetence = new CollaborateurCompetence();
        $collabCompetence->setCompetence($competence);
        $collabCompetence->setNiveau(8);
        
        // Test ajout de compétence
        $collaborateur->addCompetence($collabCompetence);
        
        $this->assertCount(1, $collaborateur->getCompetences());
        $this->assertTrue($collaborateur->getCompetences()->contains($collabCompetence));
        $this->assertEquals($collaborateur, $collabCompetence->getCollaborateur());
        
        // Test suppression de compétence
        $collaborateur->removeCompetence($collabCompetence);
        
        $this->assertCount(0, $collaborateur->getCompetences());
        $this->assertFalse($collaborateur->getCompetences()->contains($collabCompetence));
    }

    public function testCompetencesCollectionIsEmptyByDefault()
    {
        $collaborateur = new Collaborateur();
        
        $this->assertCount(0, $collaborateur->getCompetences());
    }

    public function testMultipleCompetences()
    {
        $collaborateur = new Collaborateur();
        
        $competence1 = new Competence();
        $competence1->setNom('PHP');
        $collabComp1 = new CollaborateurCompetence();
        $collabComp1->setCompetence($competence1);
        $collabComp1->setNiveau(8);
        
        $competence2 = new Competence();
        $competence2->setNom('JavaScript');
        $collabComp2 = new CollaborateurCompetence();
        $collabComp2->setCompetence($competence2);
        $collabComp2->setNiveau(6);
        
        $collaborateur->addCompetence($collabComp1);
        $collaborateur->addCompetence($collabComp2);
        
        $this->assertCount(2, $collaborateur->getCompetences());
    }

    public function testFluentInterface()
    {
        $collaborateur = new Collaborateur();
        
        $result = $collaborateur
            ->setNom('Dupont')
            ->setPrenom('Jean')
            ->setEmail('jean.dupont@example.com')
            ->setRole('Manager')
            ->setChargeActuelle(30.0)
            ->setDisponible(false);
        
        $this->assertSame($collaborateur, $result);
        $this->assertEquals('Dupont', $collaborateur->getNom());
        $this->assertEquals('Jean', $collaborateur->getPrenom());
        $this->assertEquals('jean.dupont@example.com', $collaborateur->getEmail());
        $this->assertEquals('Manager', $collaborateur->getRole());
        $this->assertEquals(30.0, $collaborateur->getChargeActuelle());
        $this->assertFalse($collaborateur->isDisponible());
    }
}
