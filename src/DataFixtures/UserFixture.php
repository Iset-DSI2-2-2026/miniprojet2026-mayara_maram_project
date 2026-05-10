<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public const USER_REFERENCE = 'user_';

    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create('fr_FR');

        // ADMIN
        $admin = new User();

        $admin->setEmail('admin@bookshelf.com');
        $admin->setPseudo('admin');
        $admin->setRoles(['ROLE_ADMIN']);

        $admin->setPassword(
            $this->passwordHasher->hashPassword(
                $admin,
                'admin123'
            )
        );

        $manager->persist($admin);

        // BIBLIO
        $biblio = new User();

        $biblio->setEmail('biblio@bookshelf.com');
        $biblio->setPseudo('bibliothecaire');
        $biblio->setRoles(['ROLE_BIBLIOTHECAIRE']);

        $biblio->setPassword(
            $this->passwordHasher->hashPassword(
                $biblio,
                'biblio123'
            )
        );

        $manager->persist($biblio);

        // USERS
        for ($i = 0; $i < 5; $i++) {

            $user = new User();

            $user->setEmail($faker->unique()->email());

            $user->setPseudo($faker->userName());

            $user->setRoles(['ROLE_USER']);

            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    'user123'
                )
            );

            $manager->persist($user);

            $this->addReference(
                self::USER_REFERENCE . $i,
                $user
            );
        }

        $manager->flush();
    }
}
