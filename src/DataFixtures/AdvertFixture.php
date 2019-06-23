<?php

namespace App\DataFixtures;

use App\Entity\Advert;
use App\Entity\Status;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AdvertFixture extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager )
    {
        $this->createMany(10, 'adverts', function() {
            $ad = new Advert();
            $ad->setName($this->faker->firstNameFemale);
            $ad->setStatus($this->getRandomReference('status'));

            return $ad;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [StatusFixture::class];
    }
}
