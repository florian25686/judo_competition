<?php

namespace AppBundle\DataFixtures\ORM\Demo;

use AppBundle\Entity\AgeGroups;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadExampleAgeGroups extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $ageU8 = new AgeGroups();
        $ageU8->setName('U8');

        $manager->persist($ageU8);

        $ageU10 = new AgeGroups();
        $ageU10->setName('U10');

        $manager->persist($ageU10);

        $manager->flush();

        $this->addReference('ag-u8', $ageU8);
        $this->addReference('ag-u10', $ageU10);
    }
}