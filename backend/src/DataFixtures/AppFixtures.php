<?php
// src/DataFixtures/AppFixtures.php
$admin = (new User())
    ->setEmail('admin@test.local')
    ->setPassword($hasher->hashPassword($user, 'Admin123!'))
    ->setRoles(['ROLE_ADMIN']);
$manager->persist($admin);
