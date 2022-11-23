<?php

namespace App\DataFixtures;

use App\Entity\User;
use DateTimeImmutable;
use App\Entity\Customer;
use App\Entity\Products;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager)
    {
        $date = new DateTimeImmutable; 
        $user = new User();
        $hashedPassword = $this->hasher->hashPassword($user, 'test');
        $user->setPassword($hashedPassword);
        $user->setEmail('test@test.com');
        $user->setRoles(['ROLE_ADMIN']);

        $manager->persist($user);
        $manager->flush();

        $produit = new Products();
        $produit->setName('Iphone');
        $produit->setColor('red');
        $produit->setDescription('Au il est beau ce téléphone');
        $produit->setPrice(750);
        $produit->setCreatedAt($date);

        $manager->persist($produit);
        $manager->flush();

        $clients = new Customer();
        $clients->setFirstname('Jeanjean');
        $clients->setLastname('DuRonchons');
        $clients->setPhoneNumber('0665499872');
        $clients->setCreatedAt($date);
        $clients->setUser($user);

        $manager->persist($clients);
        $manager->flush();
    }
}
