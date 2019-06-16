<?php

namespace App\DataFixtures;

use App\Entity\Component;
use App\Entity\Video;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class VideoFixture extends BaseFixture implements DependentFixtureInterface
{
    private static $format = [
        'mp4', 'webm'
    ];

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'video', function() {
            $component = new Video();
            $component->setComponentId($this->getRandomReference('components'));
            $component->setFormat($this->faker->randomElement(self::$format));
            $component->setLink($this->faker->url);
            $component->setWeight($this->faker->randomFloat(1, 2, 20));

            return $component;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ComponentFixture::class];
    }

}
