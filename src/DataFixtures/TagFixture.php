<?php

namespace App\DataFixtures;

use App\Entity\Tag;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TagFixture extends Fixture
{
    public const TAG_REFERENCE = 'tag_';

    public function load(ObjectManager $manager): void
    {
        $tags = [
            ['Bestseller', '#E74C3C'],
            ['Classique', '#3498DB'],
            ['Coup de cœur', '#9B59B6'],
            ['Nouveau', '#2ECC71'],
            ['Populaire', '#F39C12'],
            ['Jeunesse', '#1ABC9C'],
            ['Aventure', '#34495E'],
            ['Inspirant', '#D35400'],
        ];

        foreach ($tags as $index => $data) {

            $tag = new Tag();

            $tag->setNom($data[0]);
            $tag->setCouleur($data[1]);

            $manager->persist($tag);

            $this->addReference(
                self::TAG_REFERENCE . $index,
                $tag
            );
        }

        $manager->flush();
    }
}
