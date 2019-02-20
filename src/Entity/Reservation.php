<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serialize;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\MaxDepth;


/**
 * @ORM\Entity(repositoryClass="App\Repository\ReservationRepository")
 * @Serialize\ExclusionPolicy("none")
 */
class Reservation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $fromdate;
    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $todate;
    /**
     * @ORM\Column(type="boolean")
     */
    private $confirmed = false;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reservations_init")
     * @Serialize\MaxDepth(1)
     */
    private $user_initiator;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="reservations_admin")
     * @Serialize\MaxDepth(1)
     */
    private $reservation_admin;
    /**
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="reservations")
     * @Serialize\MaxDepth(1)
     */
    private $reservation_room;

    /**
     * @return mixed
     */
    public function getReservationRoom()
    {
        return $this->reservation_room;
    }

    /**
     * @param mixed $reservation_room
     */
    public function setReservationRoom($reservation_room): void
    {
        $this->reservation_room = $reservation_room;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getFromdate()
    {
        return $this->fromdate;
    }

    /**
     * @param mixed $fromdate
     */
    public function setFromdate($fromdate): void
    {
        $this->fromdate = $fromdate;
    }

    /**
     * @return mixed
     */
    public function getTodate()
    {
        return $this->todate;
    }

    /**
     * @param mixed $todate
     */
    public function setTodate($todate): void
    {
        $this->todate = $todate;
    }

    /**
     * @return mixed
     */
    public function getConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * @param mixed $confirmed
     */
    public function setConfirmed($confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return mixed
     */
    public function getUserInitiator()
    {
        return $this->user_initiator;
    }

    /**
     * @param mixed $user_initiator
     */
    public function setUserInitiator($user_initiator): void
    {
        $this->user_initiator = $user_initiator;
    }

    /**
     * @return mixed
     */
    public function getReservationAdmin()
    {
        return $this->reservation_admin;
    }

    /**
     * @param mixed $reservation_admin
     */
    public function setReservationAdmin($reservation_admin): void
    {
        $this->reservation_admin = $reservation_admin;
    }


}
