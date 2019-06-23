<?php

namespace App\DataFixtures;

use App\Entity\Status;
use Doctrine\Common\Persistence\ObjectManager;

class StatusFixture extends BaseFixture
{

    private static $status = array(
        'stopped',
        'published',
        'publishing',
    );

    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(3, 'status', function ()
        {
            $ad = new Status();
            $ad->setName($this->faker->randomElement(self::$status));

            return $ad;
        });

        $manager->flush();
    }

}
