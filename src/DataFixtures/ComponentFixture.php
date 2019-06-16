<?php

namespace App\DataFixtures;

use App\Entity\Component;
use App\Repository\ComponentTypeRepository;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class ComponentFixture extends BaseFixture implements DependentFixtureInterface
{
    private $typeRepository;

    public function __construct(ComponentTypeRepository $typeRepository)
    {
        $this->typeRepository = $typeRepository;
    }

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(10, 'components', function() {
            $typeList = $this->typeRepository->findAll();

            $coordinateX = $this->faker->numberBetween(0,360);
            $coordinateY = $this->faker->numberBetween(0,360);
            $coordinateZ = $this->faker->numberBetween(0,360);
            $component = new Component();
            $component->setAdvertId($this->getRandomReference('adverts'));
            $component->setHeight($this->faker->randomFloat());
            $component->setWidth($this->faker->randomFloat());
            $component->setPosition($coordinateX .' ,' . $coordinateY . ','. $coordinateZ);
            $component->setType($this->getRandomReference('types'));

            return $component;
        });

        $manager->flush();
    }

    public function getDependencies()
    {
        return [AdvertFixture::class];
    }
}
