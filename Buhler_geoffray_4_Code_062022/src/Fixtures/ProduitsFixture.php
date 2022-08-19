<?php
namespace App\DataFixtures\AppFixtures;

use App\Entity\Produit;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class ProduitsFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $produit = new Produit();
        $produit->setColor('red');
        $produit->setDescription('Il est beau');
        $produit->setName('Iphone1000');
        $manager->persist($produit);
        $manager->flush();
    }
}