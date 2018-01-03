<?php

namespace AppBundle\DataFixtures\ORM\Demo;


use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadExampleManagementUser extends AbstractFixture
{
    public function load(ObjectManager $manager)
    {
        $adminUser = new User();
        $adminUser->setUsername('admin');
        $adminUser->setPlainPassword('test');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $adminUser->setEmail('admin@example.com');
        $adminUser->setEnabled(true);

        $manager->persist($adminUser);

        $managerUser = new User();
        $managerUser->setUsername('user');
        $managerUser->setPlainPassword('test');
        $managerUser->setRoles(['ROLE_MANAGEMENT']);
        $managerUser->setEmail('manager@example.com');
        $managerUser->setEnabled(true);
        $manager->persist($managerUser);

        $manager->flush();
    }
}