<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serialize;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupRepository")
 * @ORM\Table(name="groups")
 * @Serialize\ExclusionPolicy("none")
 */
class Group
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @ORM\OneToMany(targetEntity="Room", mappedBy="group")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups_member")
     * @Serialize\MaxDepth(1)
     */
    private $members;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="groups_admin")
     * @Serialize\MaxDepth(1)
     */
    private $admins;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\OneToMany(targetEntity="Room", mappedBy="group")
     * @Serialize\MaxDepth(1)
     */
    private $rooms;

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
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User[]|\Doctrine\Common\Collections\Collection
     */
    public function getMembers()
    {
        return $this->members;
    }

    /**
     * @param User[]|\Doctrine\Common\Collections\Collection $members
     */
    public function setMembers($members): void
    {
        $this->members = $members;
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
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRooms(): \Doctrine\Common\Collections\Collection
    {
        return $this->rooms;
    }

    /**
     * @param \Doctrine\Common\Collections\Collection $rooms
     */
    public function setRooms(\Doctrine\Common\Collections\Collection $rooms): void
    {
        $this->rooms = $rooms;
    }
    public function addMember($user) {
        $this->getMembers()->add($user);
    }

    public function deleteMember($user) {
        $this->getMembers()->removeElement($user);
    }

    public function addAdmin($user) {
        $this->getAdmins()->add($user);
    }

    public function deleteAdmin($user) {
        $this->getAdmins()->removeElement($user);
    }

    public function addRoom($room) {
        $this->getRooms()->add($room);
    }

    public function deleteRoom($room) {
        $this->getRooms()->removeElement($room);
    }

    public function isAdminOfGroup(User $user)
    {
        return $this->getAdmins()->contains($user);
    }
}
