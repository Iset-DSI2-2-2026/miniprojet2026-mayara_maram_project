<?php

namespace App\DataFixtures;

use App\Entity\Genre;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class GenreFixture extends Fixture
{
    public const GENRE_REFERENCE = 'genre_';

    public function load(ObjectManager $manager): void
    {
        $genres = [
            ['Roman', '#FF5733'],
            ['SF', '#3498DB'],
            ['Policier', '#2ECC71'],
            ['Fantasy', '#9B59B6'],
            ['Biographie', '#F39C12'],
            ['Histoire', '#1ABC9C'],
        ];

        foreach ($genres as $index => $data) {

            $genre = new Genre();

            $genre->setNom($data[0]);
            $genre->setCouleur($data[1]);
            $genre->setDescription('Description du genre ' . $data[0]);

            $manager->persist($genre);

            // référence
            $this->addReference(self::GENRE_REFERENCE . $index, $genre);
        }

        $manager->flush();
    }
}