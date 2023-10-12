<?php

namespace App\Persistence\Fixture;

use App\Persistence\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class UserFixture extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $hasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user
            ->setNickname('tester')
            ->setPassword($this->hasher->hashPassword($user, 'tester'))
        ;

        $manager->persist($user);
        $manager->flush();
    }
}