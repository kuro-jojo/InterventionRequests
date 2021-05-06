<?php

namespace App\Entity;

use App\Repository\ChefPoleRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChefPoleRepository::class)
 */
class ChefPole extends Responsable
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Pole::class, inversedBy="chefPole", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $monPole;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMonPole(): ?Pole
    {
        return $this->monPole;
    }

    public function setMonPole(Pole $monPole): self
    {
        $this->monPole = $monPole;

        return $this;
    }
}
