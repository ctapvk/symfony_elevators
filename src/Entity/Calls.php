<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CallsRepository")
 */
class Calls
{

    const CALL_STARTED = 1;
    const CALL_FINISHED = 2;
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $floor_from;

    /**
     * @ORM\Column(type="integer")
     */
    private $floor_to;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Elevators", inversedBy="calls")
     */
    private $elevator;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getFloorFrom(): ?int
    {
        return $this->floor_from;
    }

    public function setFloorFrom(int $floor_from): self
    {
        $this->floor_from = $floor_from;

        return $this;
    }

    public function getFloorTo(): ?int
    {
        return $this->floor_to;
    }

    public function setFloorTo(int $floor_to): self
    {
        $this->floor_to = $floor_to;

        return $this;
    }

    public function getElevator(): ?Elevators
    {
        return $this->elevator;
    }

    public function setElevator(?Elevators $elevator): self
    {
        $this->elevator = $elevator;

        return $this;
    }
}
