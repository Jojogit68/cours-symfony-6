<?php

namespace App\DataFixtures;

use App\Entity\Annonce;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory as Faker;

class AnnonceFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker::create('fr_FR');
        for ($i=0; $i < 1000; $i++) {
            $annonce = new Annonce();
            $annonce
                ->setTitle($faker->words(3, true))
                ->setDescription($faker->sentences(3, true))
                ->setPrice($faker->numberBetween(10, 100))
                ->setStatus($faker->numberBetween(0, 4))
                ->setIsSold(false)
            ;
            $manager->persist($annonce);
        }

        $manager->flush();
    }
}
