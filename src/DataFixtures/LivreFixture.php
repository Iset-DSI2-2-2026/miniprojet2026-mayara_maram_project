<?php

namespace App\DataFixtures;

use App\DataFixtures\AuteurFixture;
use App\DataFixtures\TagFixture;
use App\Entity\Livre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class LivreFixture extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        for ($i = 0; $i < 30; $i++) {

            $livre = new Livre();

            $livre->setTitre($faker->sentence(3))
                  ->setResume($faker->paragraph(4))
                  ->setIsbn($faker->isbn13())
                  ->setNbPages($faker->numberBetween(100, 1200))
                  ->setDatePublication($faker->dateTimeBetween('-10 years'))
                  ->setDisponible($faker->boolean());

            // auteur
         $livre->setAuteur(
    $this->getReference(
        'auteur_' . rand(0, 4),
        \App\Entity\Auteur::class
    )
);

            // genre
         $livre->setGenre(
    $this->getReference(
        'genre_' . rand(0, 5),
        \App\Entity\Genre::class
    )
);

            // user
          $livre->setAjoutePar(
    $this->getReference(
        'user_' . rand(0, 4),
        \App\Entity\User::class
    )
);

            // tags (1 à 4 tags)
          $livre->addTag(
    $this->getReference(
        'tag_' . rand(0, 7),
        \App\Entity\Tag::class
    )
);

            $manager->persist($livre);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            GenreFixture::class,
            AuteurFixture::class,
            TagFixture::class,
            UserFixture::class,
        ];
    }
}