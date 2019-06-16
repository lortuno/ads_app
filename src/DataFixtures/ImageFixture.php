<?php

namespace App\DataFixtures;

use App\Entity\Image;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ImageFixture extends BaseFixture implements DependentFixtureInterface
{
    private static $format = [
        'jpg', 'png'
    ];

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'image', function() {
            $component = new Image();
            $component->setComponentId($this->getRandomReference('components'));
            $component->setFormat($this->faker->randomElement(self::$format));
            $component->setLink($this->faker->url);
            $component->setWeight($this->faker->randomFloat(1, 1, 10));

            return $component;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ComponentFixture::class];
    }
}
