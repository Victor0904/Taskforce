<?php

namespace App\DataFixtures;

use App\Entity\Collaborateur;
use App\Entity\Mission;
use App\Entity\Affectation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $collaborateurs = [];

        // Collaborateurs
        for ($i = 0; $i < 5; $i++) {
            $collab = new Collaborateur();
            $collab->setNom($faker->lastName());
            $collab->setPrenom($faker->firstName());
            $collab->setEmail($faker->unique()->email());
            $collab->setDateNaissance($faker->dateTimeBetween('-50 years', '-20 years'));
            $collab->setPoste($faker->jobTitle());
            $collab->setActif(true);
            $manager->persist($collab);
            $collaborateurs[] = $collab;
        }

        $missions = [];

        // Missions
        for ($i = 0; $i < 3; $i++) {
            $mission = new Mission();
            $mission->setTitre($faker->sentence(3));            // requis
            $mission->setDescription($faker->paragraph());      // requis
            $mission->setNom($faker->sentence(3));              // facultatif si tu veux
            $mission->setDateDebut($faker->dateTimeBetween('-1 year', 'now'));
            $mission->setDateFin($faker->dateTimeBetween('now', '+1 year'));
            $manager->persist($mission);
            $missions[] = $mission;
        }

        // Affectations
        foreach ($collaborateurs as $collab) {
            $assignedMissions = $faker->randomElements($missions, 2);
            foreach ($assignedMissions as $mission) {
                $aff = new Affectation();
                $aff->setCollaborateur($collab);
                $aff->setMission($mission);
                $aff->setRole($faker->jobTitle());
                $aff->setDateAffectation($faker->dateTimeThisYear());
                $manager->persist($aff);
            }
        }

        $manager->flush();
    }
}
