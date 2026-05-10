<?php

namespace App\DataFixtures;

use App\Entity\Auteur;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class AuteurFixture extends Fixture
{
    public const AUTEUR_REFERENCE = 'auteur_';

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        $nationalites = [
            'Française',
            'Tunisienne',
            'Canadienne',
            'Marocaine',
            'Algérienne'
        ];

        for ($i = 0; $i < 5; $i++) {

            $auteur = new Auteur();

            $auteur->setNom($faker->lastName());
            $auteur->setPrenom($faker->firstName());
            $auteur->setBiographie($faker->paragraph(5));
            $auteur->setNationalite(
                $faker->randomElement($nationalites)
            );

            $manager->persist($auteur);

            $this->addReference(
                self::AUTEUR_REFERENCE . $i,
                $auteur
            );
        }

        $manager->flush();
    }
}
