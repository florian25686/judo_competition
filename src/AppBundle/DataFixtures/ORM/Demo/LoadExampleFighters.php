<?php

namespace AppBundle\DataFixtures\ORM\Demo;

use AppBundle\Entity\Fighter;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\Tests\Compiler\F;

class LoadExampleFighters extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $user1 = new Fighter();
        $user1->setFirstName('Anne');
        $user1->setLastName('Frankfurter');
        $user1->setGender('w');
        $user1->setAgeGroup($this->getReference('ag-u8'));
        $user1->setClub('UJC Dornbirn');
        $user1->setWeight('20,5');
        $user1->setBirthDate(new \DateTime('2010-01-01'));
        $user1->setInFight(false);
        $manager->persist($user1);

        $user2 = new Fighter();
        $user2->setFirstName('Lukas');
        $user2->setLastName('Schäfer');
        $user2->setGender('m');
        $user2->setAgeGroup($this->getReference('ag-u8'));
        $user2->setClub('UJC Dornbirn');
        $user2->setWeight('23,3');
        $user2->setBirthDate(new \DateTime('2011-01-01'));
        $user2->setInFight(false);
        $manager->persist($user2);

        $user3 = new Fighter();
        $user3->setFirstName('Robert');
        $user3->setLastName('Faber');
        $user3->setGender('m');
        $user3->setAgeGroup($this->getReference('ag-u8'));
        $user3->setClub('UJC Feldkirch');
        $user3->setWeight('22,85');
        $user3->setBirthDate(new \DateTime('2010-01-01'));
        $user3->setInFight(false);
        $manager->persist($user3);

        $user4 = new Fighter();
        $user4->setFirstName('Leon');
        $user4->setLastName('Bach');
        $user4->setGender('m');
        $user4->setAgeGroup($this->getReference('ag-u8'));
        $user4->setClub('JC Wangen');
        $user4->setWeight('24');
        $user4->setBirthDate(new \DateTime('2010-01-01'));
        $user4->setInFight(false);
        $manager->persist($user4);

        $user5 = new Fighter();
        $user5->setFirstName('Antje');
        $user5->setLastName('Bergmann');
        $user5->setGender('w');
        $user5->setAgeGroup($this->getReference('ag-u8'));
        $user5->setClub('JC Ravensburg');
        $user5->setWeight('22,9');
        $user5->setBirthDate(new \DateTime('2010-01-01'));
        $user5->setInFight(false);
        $manager->persist($user5);

        $user6 = new Fighter();
        $user6->setFirstName('Steffen');
        $user6->setLastName('Ritter');
        $user6->setGender('m');
        $user6->setAgeGroup($this->getReference('ag-u10'));
        $user6->setClub('UJC Dornbirn');
        $user6->setWeight('32');
        $user6->setBirthDate(new \DateTime('2009-01-01'));
        $user6->setInFight(false);
        $manager->persist($user6);

        $user7 = new Fighter();
        $user7->setFirstName('Martin');
        $user7->setLastName('Abt');
        $user7->setGender('m');
        $user7->setAgeGroup($this->getReference('ag-u10'));
        $user7->setClub('UJC Feldkirch');
        $user7->setWeight('33,12');
        $user7->setBirthDate(new \DateTime('2009-01-01'));
        $user7->setInFight(false);
        $manager->persist($user7);

        $manager->flush();
    }
}