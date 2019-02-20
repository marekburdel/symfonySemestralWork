<?php

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\User\UserInterface;
use JMS\Serializer\Annotation as Serialize;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Exclude;
use JMS\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ExclusionPolicy("none")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     */
    private $username;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     * @Exclude
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $surname;

    /**
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Assert\Length(max=50)
     */
    private $email;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $expirationDate;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="members")
     * @ORM\JoinTable(name="groups_members")
     * @Serialize\MaxDepth(1)
     */
    private $groups_member;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="admins")
     * @ORM\JoinTable(name="groups_admins")
     * @Serialize\MaxDepth(1)
     */
    private $groups_admin;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Room", inversedBy="users")
     * @ORM\JoinTable(name="rooms_users")
     * @Serialize\MaxDepth(1)
     */
    private $rooms_user;

    /**
     * @var \Doctrine\Common\Collections\Collection
     * @ORM\ManyToMany(targetEntity="Room", inversedBy="admins")
     * @ORM\JoinTable(name="rooms_admins")
     * @Serialize\MaxDepth(1)
     */
    private $rooms_admin;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="user_initiator")
     * @Serialize\MaxDepth(1)
     */
    private $reservations_init;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     * @ORM\OneToMany(targetEntity="Reservation", mappedBy="reservation_admin")
     * @Serialize\MaxDepth(1)
     */
    private $reservations_admin;

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
     * @return mixed
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param mixed $surname
     */
    public function setSurname($surname): void
    {
        $this->surname = $surname;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->username;
    }

    /**
     * @param string $username
     * @return User
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_SIGNED_USER';

        return array_unique($roles);
    }

    /**
     * @param mixed $roles
     */
    public function setRoles($roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    /**
     * @param string $password
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param mixed $expirationDate
     */
    public function setExpirationDate($expirationDate): void
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return Group[]|Collection
     */
    public function getGroupsMember()
    {
        return $this->groups_member;
    }
    public function getGroups_Member()
    {
        return $this->getGroupsMember();
    }
    /**
     * @param Group[]|Collection $groups_member
     */
    public function setGroupsMember($groups_member): void
    {
        $this->groups_member = $groups_member;
    }

    /**
     * @return Group[]|Collection
     */
    public function getGroupsAdmin()
    {
        return $this->groups_admin;
    }
    public function getGroups_Admin()
    {
        return $this->getGroupsAdmin();
    }

    /**
     * @param Group[]|Collection
     */
    public function setGroupsAdmin($groups_admin): void
    {
        $this->groups_admin = $groups_admin;
    }

    /**
     * @return Collection
     */
    public function getRoomsUser(): Collection
    {
        return $this->rooms_user;
    }
    public function getRooms_User()
    {
        return $this->getRoomsUser();
    }

    /**
     * @param Collection $rooms_user
     */
    public function setRoomsUser(Collection $rooms_user): void
    {
        $this->rooms_user = $rooms_user;
    }

    /**
     * @return Collection
     */
    public function getRoomsAdmin(): Collection
    {
        return $this->rooms_admin;
    }
    public function getRooms_Admin()
    {
        return $this->getRoomsAdmin();
    }

    /**
     * @param Collection $rooms_admin
     */
    public function setRoomsAdmin(Collection $rooms_admin): void
    {
        $this->rooms_admin = $rooms_admin;
    }

    /**
     * @return Collection
     */
    public function getReservationsInit(): Collection
    {
        return $this->reservations_init;
    }
    public function getReservations_Init()
    {
        return $this->getReservationsInit();
    }

    /**
     * @param Collection $reservations_init
     */
    public function setReservationsInit(Collection $reservations_init): void
    {
        $this->reservations_init = $reservations_init;
    }

    /**
     * @return Collection
     */
    public function getReservationsAdmin(): Collection
    {
        return $this->reservations_admin;
    }

    /**
     * @param Collection $reservations_admin
     */
    public function setReservationsAdmin(Collection $reservations_admin): void
    {
        $this->reservations_admin = $reservations_admin;
    }

    public function addGroupMember($group)
    {
        $this->getGroups_Member()->add($group);
    }
    public function deleteGroupMember($group)
    {
        $this->getGroups_Member()->removeElement($group);
    }
    public function addGroupAdmin($group)
    {
        $this->getGroups_Admin()->add($group);
    }
    public function deleteGroupAdmin($group)
    {
        $this->getGroups_Admin()->removeElement($group);
    }
    public function addRoomUser($group)
    {
        $this->getRooms_User()->add($group);
    }
    public function deleteRoomUser($group)
    {
        $this->getRooms_User()->removeElement($group);
    }
    public function addRoomAdmin($group)
    {
        $this->getRooms_Admin()->add($group);
    }
    public function deleteRoomAdmin($group)
    {
        $this->getRooms_Admin()->removeElement($group);
    }
    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }
}
