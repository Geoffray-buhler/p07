<?php
namespace App\DataFixtures\AppFixtures;

use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setEmail("test@test.com");
        $user->setUsername("test");
        $user->setPassword(password_hash("test",PASSWORD_DEFAULT));
        $manager->persist($user);
        $manager->flush();
    }
}