<?php

namespace App\DataFixtures;

use App\Entity\Salon;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
class SalonFixtures extends Fixture
{
    /** @var Generator */
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $count =100;
        $this->faker = Factory::create();

        for($i=0;$i<=$count;$i++){
            $product = new Salon();
            $product->setName($this->faker->company)
                ->setEmail($this->faker->companyEmail)
                ->setCity($this->faker->city)
                ->setPhone($this->faker->phoneNumber)
                ->setStreet($this->faker->streetName)
                ->setZip($this->faker->postcode);
            $this->addReference(Salon::class . '_' . $i, $product);
            $manager->persist($product);
        }
        $manager->flush();
    }
}
