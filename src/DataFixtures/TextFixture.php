<?php

namespace App\DataFixtures;

use App\Entity\Component;
use App\Entity\Text;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class TextFixture extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(10, 'text', function() {
            $component = new Text();
            $component->setComponentId($this->getRandomReference('components'));
            $component->setValue($this->faker->text(140));

            return $component;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [ComponentFixture::class];
    }
}
