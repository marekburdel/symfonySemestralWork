<?php

namespace App\DataFixtures;

use App\Entity\Reservation;
use App\Entity\User;
use App\Repository\ReservationRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setUsername('admin');
        $user->setPassword($this->passwordEncoder->encodePassword($user, 'admin'));
        $user->setName('Name');
        $user->setSurname('Surname');
        $user->setEmail('admin@fit.cvut.cz');
        $user->setExpirationDate(null);
        $user->setRoles(['ROLE_SIGNED_USER', 'ROLE_ADMIN']);

        $user2 = new User();
        $user2->setUsername('user');
        $user2->setPassword($this->passwordEncoder->encodePassword($user, 'user'));
        $user2->setName('Nameu');
        $user2->setSurname('Surnameu');
        $user2->setEmail('user@fit.cvut.cz');
        $user2->setExpirationDate(null);

        $manager->persist($user);
        $manager->persist($user2);
        $manager->flush();

        //doctrine:fixtures:load --append
    }
}
