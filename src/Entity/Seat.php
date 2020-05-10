<?php


namespace App\Entity;


use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Seat
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\SeatRepository")
 */
class Seat
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="bigint")
     * @var int
     */
    private $id;

    /**
     * @ORM\Column(type="bigint")
     * @var int
     */
    private $seatNum;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $bookedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="seats")
     * @var User|null
     */
    private $bookedBy = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $selledAt = null;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @var DateTime|null
     */
    private $returnedAt = null;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Flight", inversedBy="seats")
     * @var Flight
     */
    private $flight;

    public function getId(): ?string
    {
        return $this->id;
    }

    public function getSeatNum(): ?string
    {
        return $this->seatNum;
    }

    public function setSeatNum(string $seatNum): self
    {
        $this->seatNum = $seatNum;

        return $this;
    }

    public function getBookedAt(): ?\DateTimeInterface
    {
        return $this->bookedAt;
    }

    public function setBookedAt(?\DateTimeInterface $bookedAt): self
    {
        $this->bookedAt = $bookedAt;

        return $this;
    }

    public function getSelledAt(): ?\DateTimeInterface
    {
        return $this->selledAt;
    }

    public function setSelledAt(?\DateTimeInterface $selledAt): self
    {
        $this->selledAt = $selledAt;

        return $this;
    }

    public function getReturnedAt(): ?\DateTimeInterface
    {
        return $this->returnedAt;
    }

    public function setReturnedAt(?\DateTimeInterface $returnedAt): self
    {
        $this->returnedAt = $returnedAt;

        return $this;
    }

    public function getFlight(): ?Flight
    {
        return $this->flight;
    }

    public function setFlight(?Flight $flight): self
    {
        $this->flight = $flight;

        return $this;
    }

    public function getBookedBy(): ?User
    {
        return $this->bookedBy;
    }

    public function setBookedBy(?User $bookedBy): self
    {
        $this->bookedBy = $bookedBy;

        return $this;
    }
}
