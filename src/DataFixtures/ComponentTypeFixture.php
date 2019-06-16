<?php

namespace App\DataFixtures;

use App\Entity\ComponentTypeFoo;
use Doctrine\Common\Persistence\ObjectManager;

class ComponentTypeFixture extends BaseFixture
{

    private static $type = array(
        'image',
        'text',
        'video',
    );

    protected function loadData(ObjectManager $manager)
    {

        $this->createMany(3, 'types', function ()
        {

            $ad = new ComponentTypeFoo();
            $type = $this->faker->randomElement(self::$type);
            $ad->setName($type);
            $ad->setDescription(sprintf('this is a %s component', $type));

            return $ad;

        });

        $manager->flush();
    }
}
