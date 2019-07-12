<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\HousesRepository")
 */
class Houses
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $floors;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Elevators", mappedBy="house")
     */
    private $elevators;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $last_call_at;

    public function __construct()
    {
        $this->elevators = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFloors(): ?int
    {
        return $this->floors;
    }

    public function setFloors(int $floors): self
    {
        $this->floors = $floors;

        return $this;
    }

    /**
     * @return Collection|Elevators[]
     */
    public function getElevators(): Collection
    {
        return $this->elevators;
    }

    public function addElevator(Elevators $elevator): self
    {
        if (!$this->elevators->contains($elevator)) {
            $this->elevators[] = $elevator;
            $elevator->setHouse($this);
        }

        return $this;
    }

    public function removeElevator(Elevators $elevator): self
    {
        if ($this->elevators->contains($elevator)) {
            $this->elevators->removeElement($elevator);
            // set the owning side to null (unless already changed)
            if ($elevator->getHouse() === $this) {
                $elevator->setHouse(null);
            }
        }

        return $this;
    }

    public function __toString(){
        return $this->name;
    }

    public function getLastCallAt(): ?\DateTimeInterface
    {
        return $this->last_call_at;
    }

    public function setLastCallAt(?\DateTimeInterface $last_call_at): self
    {
        $this->last_call_at = $last_call_at;

        return $this;
    }

}
