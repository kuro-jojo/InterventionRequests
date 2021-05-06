<?php

namespace App\Entity;

use App\Repository\AgentMaintenanceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * 
 * @ORM\Entity(repositoryClass=AgentMaintenanceRepository::class)
 * 
 */
class AgentMaintenance extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=Pole::class, inversedBy="agentMaintenances")
     */
    private $mesPoles;

    /**
     * @ORM\ManyToMany(targetEntity=DemandeIntervention::class, mappedBy="traiteursDemande")
     */
    private $demandeInterventions;

    public function __construct()
    {
        $this->mesPoles = new ArrayCollection();
        $this->demandeInterventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Pole[]
     */
    public function getMesPoles(): Collection
    {
        return $this->mesPoles;
    }

    public function addMesPole(Pole $mesPole): self
    {
        if (!$this->mesPoles->contains($mesPole)) {
            $this->mesPoles[] = $mesPole;
        }

        return $this;
    }

    public function removeMesPole(Pole $mesPole): self
    {
        $this->mesPoles->removeElement($mesPole);

        return $this;
    }

    /**
     * @return Collection|DemandeIntervention[]
     */
    public function getDemandeInterventions(): Collection
    {
        return $this->demandeInterventions;
    }

    public function addDemandeIntervention(DemandeIntervention $demandeIntervention): self
    {
        if (!$this->demandeInterventions->contains($demandeIntervention)) {
            $this->demandeInterventions[] = $demandeIntervention;
            $demandeIntervention->addTraiteursDemande($this);
        }

        return $this;
    }

    public function removeDemandeIntervention(DemandeIntervention $demandeIntervention): self
    {
        if ($this->demandeInterventions->removeElement($demandeIntervention)) {
            $demandeIntervention->removeTraiteursDemande($this);
        }

        return $this;
    }

  
}
