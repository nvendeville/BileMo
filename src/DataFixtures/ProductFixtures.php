<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 30; $i++) {
            $product = new Product();
            $product->setName('Téléphone' . $i);
            $product->setDescription(('Ceci est la plus magnifique description de téléphone' . $i . 'du monde'));
            $product->setReference('REFTEL' . $i);
            $product->setAdded(new \DateTime('now'));
            $product->setUpdated(new \DateTime('now'));
            $product->setPrice(mt_rand(99, 649));
            $manager->persist($product);
        }
        $manager->flush();
    }
}
