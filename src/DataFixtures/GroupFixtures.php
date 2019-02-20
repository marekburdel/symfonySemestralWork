<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class GroupFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $group = new Group();
        $group->setName("Grupa");

        $manager->persist($group);
        $manager->flush();

        //doctrine:fixtures:load --append
    }
}
