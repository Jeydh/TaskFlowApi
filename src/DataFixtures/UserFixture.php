<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixture extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user1 = new User();
        $user1->setEmail('test@example.com');
        $user1->setPassword($this->passwordHasher->hashPassword($user1, 'test1234'));
        $user1->setRoles(['ROLE_USER']);
        $manager->persist($user1);

        $user2 = new User();
        $user2->setEmail('newuser@example.com');
        $user2->setPassword($this->passwordHasher->hashPassword($user2, 'test1234'));
        $user2->setRoles(['ROLE_USER']);
        $manager->persist($user2);  

        $this->addReference('user1', $user1);
        $this->addReference('user2', $user2);

        $manager->flush();
    }
}
