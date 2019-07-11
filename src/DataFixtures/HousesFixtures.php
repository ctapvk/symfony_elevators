<?php

namespace App\DataFixtures;

use App\Entity\Elevators;
use App\Entity\Houses;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class HousesFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        for ($i = 1; $i <= 100; $i++) {
            $house = new Houses();
            $house->setName("Дом № $i");
            $house->setFloors(rand(1, 10));
            $manager->persist($house);
        }
        $manager->flush();

        for ($i = 1; $i <= 4; $i++) {
            $elevetor = new Elevators();
            $elevetor->setName("Лифт № $i");
            $elevetor->setPosition(rand(1, 10));
            $elevetor->setHouse($house);
            $elevetor->setStatus(1);
            $manager->persist($elevetor);
        }
        $manager->flush();

    }
}
