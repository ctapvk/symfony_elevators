<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ElevatorsRepository")
 */
class Elevators
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Houses", inversedBy="elevators")
     */
    private $house;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
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

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getHouse(): ?Houses
    {
        return $this->house;
    }

    public function setHouse(?Houses $house): self
    {
        $this->house = $house;

        return $this;
    }

    /**
     * Generates the magic method
     *
     */
    public function __toString(){
        // to show the name of the Category in the select
        return $this->name;
        // to show the id of the Category in the select
        // return $this->id;
    }
}
