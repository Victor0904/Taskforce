<?php

namespace App\Tests\Entity;

use App\Entity\CollaborateurCompetence;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use PHPUnit\Framework\TestCase;

class CollaborateurCompetenceTest extends TestCase
{
    public function testCollaborateurCompetenceCreation()
    {
        $collabCompetence = new CollaborateurCompetence();
        
        $this->assertInstanceOf(CollaborateurCompetence::class, $collabCompetence);
        $this->assertNull($collabCompetence->getId());
    }

    public function testCollaborateurSetterAndGetter()
    {
        $collabCompetence = new CollaborateurCompetence();
        $collaborateur = new Collaborateur();
        $collaborateur->setNom('Dupont');
        $collaborateur->setPrenom('Jean');
        
        $collabCompetence->setCollaborateur($collaborateur);
        
        $this->assertEquals($collaborateur, $collabCompetence->getCollaborateur());
    }

    public function testCompetenceSetterAndGetter()
    {
        $collabCompetence = new CollaborateurCompetence();
        $competence = new Competence();
        $competence->setNom('PHP');
        
        $collabCompetence->setCompetence($competence);
        
        $this->assertEquals($competence, $collabCompetence->getCompetence());
    }

    public function testNiveauSetterAndGetter()
    {
        $collabCompetence = new CollaborateurCompetence();
        $niveau = 8;
        
        $collabCompetence->setNiveau($niveau);
        
        $this->assertEquals($niveau, $collabCompetence->getNiveau());
    }

    public function testFluentInterface()
    {
        $collabCompetence = new CollaborateurCompetence();
        $collaborateur = new Collaborateur();
        $competence = new Competence();
        
        $result = $collabCompetence
            ->setCollaborateur($collaborateur)
            ->setCompetence($competence)
            ->setNiveau(7);
        
        $this->assertSame($collabCompetence, $result);
        $this->assertEquals($collaborateur, $collabCompetence->getCollaborateur());
        $this->assertEquals($competence, $collabCompetence->getCompetence());
        $this->assertEquals(7, $collabCompetence->getNiveau());
    }

    public function testCompleteSetup()
    {
        $collabCompetence = new CollaborateurCompetence();
        
        $collaborateur = new Collaborateur();
        $collaborateur->setNom('Martin');
        $collaborateur->setPrenom('Sophie');
        $collaborateur->setEmail('sophie.martin@example.com');
        
        $competence = new Competence();
        $competence->setNom('Vue.js');
        $competence->setDescription('Framework JavaScript');
        
        $collabCompetence->setCollaborateur($collaborateur)
            ->setCompetence($competence)
            ->setNiveau(9);
        
        $this->assertEquals($collaborateur, $collabCompetence->getCollaborateur());
        $this->assertEquals($competence, $collabCompetence->getCompetence());
        $this->assertEquals(9, $collabCompetence->getNiveau());
    }

    public function testNiveauBoundaries()
    {
        $collabCompetence = new CollaborateurCompetence();
        $competence = new Competence();
        $competence->setNom('Test');
        
        // Test niveau minimum
        $collabCompetence->setNiveau(0);
        $this->assertEquals(0, $collabCompetence->getNiveau());
        
        // Test niveau maximum
        $collabCompetence->setNiveau(10);
        $this->assertEquals(10, $collabCompetence->getNiveau());
        
        // Test niveau intermédiaire
        $collabCompetence->setNiveau(5);
        $this->assertEquals(5, $collabCompetence->getNiveau());
    }

    public function testNullCollaborateur()
    {
        $collabCompetence = new CollaborateurCompetence();
        
        $this->assertNull($collabCompetence->getCollaborateur());
        
        $collabCompetence->setCollaborateur(null);
        
        $this->assertNull($collabCompetence->getCollaborateur());
    }

    public function testNullCompetence()
    {
        $collabCompetence = new CollaborateurCompetence();
        
        $this->assertNull($collabCompetence->getCompetence());
        
        $collabCompetence->setCompetence(null);
        
        $this->assertNull($collabCompetence->getCompetence());
    }
}
