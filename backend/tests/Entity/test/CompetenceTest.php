<?php

namespace App\Tests\Entity;

use App\Entity\Competence;
use PHPUnit\Framework\TestCase;

class CompetenceTest extends TestCase
{
    public function testCompetenceCreation()
    {
        $competence = new Competence();
        
        $this->assertInstanceOf(Competence::class, $competence);
        $this->assertNull($competence->getId());
    }

    public function testNomSetterAndGetter()
    {
        $competence = new Competence();
        $nom = 'PHP';
        
        $competence->setNom($nom);
        
        $this->assertEquals($nom, $competence->getNom());
    }

    public function testDescriptionSetterAndGetter()
    {
        $competence = new Competence();
        $description = 'Langage de programmation web côté serveur';
        
        $competence->setDescription($description);
        
        $this->assertEquals($description, $competence->getDescription());
    }

    public function testDescriptionNullable()
    {
        $competence = new Competence();
        
        $this->assertNull($competence->getDescription());
        
        $competence->setDescription(null);
        
        $this->assertNull($competence->getDescription());
    }

    public function testFluentInterface()
    {
        $competence = new Competence();
        
        $result = $competence
            ->setNom('JavaScript')
            ->setDescription('Langage de programmation web côté client');
        
        $this->assertSame($competence, $result);
        $this->assertEquals('JavaScript', $competence->getNom());
        $this->assertEquals('Langage de programmation web côté client', $competence->getDescription());
    }

    public function testCompleteCompetenceSetup()
    {
        $competence = new Competence();
        
        $competence->setNom('Vue.js')
            ->setDescription('Framework JavaScript progressif pour construire des interfaces utilisateur');
        
        $this->assertEquals('Vue.js', $competence->getNom());
        $this->assertEquals('Framework JavaScript progressif pour construire des interfaces utilisateur', $competence->getDescription());
    }

    public function testEmptyDescription()
    {
        $competence = new Competence();
        $competence->setNom('React');
        $competence->setDescription('');
        
        $this->assertEquals('React', $competence->getNom());
        $this->assertEquals('', $competence->getDescription());
    }

    public function testLongDescription()
    {
        $competence = new Competence();
        $longDescription = str_repeat('Description très longue. ', 50);
        
        $competence->setNom('Symfony')
            ->setDescription($longDescription);
        
        $this->assertEquals('Symfony', $competence->getNom());
        $this->assertEquals($longDescription, $competence->getDescription());
    }
}
