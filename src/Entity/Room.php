<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Group;
use JMS\Serializer\Annotation as Serialize;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\MaxDepth;


/**
 * @ORM\Entity(repositoryClass="App\Repository\RoomRepository")
 * @Serialize\ExclusionPolicy("none")
 */
class Room
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\ManyToMany(targetEntity="room_admin", mappedBy="roomid")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     * @ORM\ManyToMany(targetEntity="User", mappedBy="rooms_user")
     * @Serialize\MaxDepth(1)
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection|User[]
     * @ORM\ManyToMany(targetEntity="User", mappedBy="rooms_admin")
     * @Serialize\MaxDepth(1)
     */
    private $admins;

    /**
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="rooms")
     * @Serialize\MaxDepth(1)
     */
    private $group;

    /**
     * @var \Doctrine\Common\Collections\Collection|Reservation[]
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="reservation_room")
     * @Serialize\MaxDepth(1)
     */
    private $reservations;

    /**
     * @return Reservation[]|\Doctrine\Common\Collections\Collection
     */
    public function getReservations()
    {
        return $this->reservations;
    }

    /**
     * @param Reservation[]|\Doctrine\Common\Collections\Collection $reservations
     */
    public function setReservations($reservations): void
    {
        $this->reservations = $reservations;
    }
    public function addReservation($reservation) {
        $this->getReservations()->add($reservation);
    }

    public function deleteReservation($reservation) {
        $this->getReservations()->removeElement($reservation);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return User[]|\Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User[]|\Doctrine\Common\Collections\Collection $users
     */
    public function setUsers($users): void
    {
        $this->users = $users;
    }

    public function addUser($user)
    {
        $this->getUsers()->add($user);
    }

    public function deleteUser($room) {
        $this->getUsers()->removeElement($room);
    }

    public function addAdmin($user)
    {
        $this->getAdmins()->add($user);
    }

    public function deleteAdmin($room) {
        $this->getAdmins()->removeElement($room);
    }

    /**
     * @return User[]|\Doctrine\Common\Collections\Collection
     */
    public function getAdmins()
    {
        return $this->admins;
    }

    /**
     * @param User[]|\Doctrine\Common\Collections\Collection $admins
     */
    public function setAdmins($admins): void
    {
        $this->admins = $admins;
    }

    /**
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param mixed $group
     */
    public function setGroup($group): void
    {
        $this->group = $group;
    }

    public function isAdminOfRoom(User $user)
    {
        return $this->getAdmins()->contains($user);
    }
}
