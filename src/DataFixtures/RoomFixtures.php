<?php

namespace App\DataFixtures;

use App\Entity\Room;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class RoomFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        $room = new Room();
        $room->setName('T9:105');
        $room2 = new Room();
        $room2->setName('T9:107');
        $room3 = new Room();
        $room3->setName('T9:155');
        $room4 = new Room();
        $room4->setName('T9:301');
        $room5 = new Room();
        $room5->setName('T9:302');

        $manager->persist($room);
        $manager->persist($room2);
        $manager->persist($room3);
        $manager->persist($room4);
        $manager->persist($room5);

        $manager->flush();
    }
}
