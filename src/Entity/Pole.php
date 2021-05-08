<?php

namespace App\Entity;

use App\DBAL\Types\PoleType;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\PoleRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
/**
 * @ORM\Entity(repositoryClass=PoleRepository::class)
 */
class Pole
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="PoleType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\PoleType")
     */
    private $nomPole;

    /**
     * @ORM\ManyToMany(targetEntity=AgentMaintenance::class, mappedBy="mesPoles")
     */
    private $agentMaintenances;

    /**
     * @ORM\OneToOne(targetEntity=ChefPole::class, mappedBy="monPole", cascade={"persist", "remove"})
     */
    private $chefPole;

    /**
     * @ORM\OneToMany(targetEntity=DemandeIntervention::class, mappedBy="poleConcerne")
     */
    private $demandeInterventions;

    public function __construct()
    {
        $this->agentMaintenances = new ArrayCollection();
        $this->demandeInterventions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomPole(): string
    {
        return $this->nomPole;
    }

    public function setNomPole(string $nomPole): self
    {
        $this->nomPole = $nomPole;

        return $this;
    }

    /**
     * @return Collection|AgentMaintenance[]
     */
    public function getAgentMaintenances(): Collection
    {
        return $this->agentMaintenances;
    }

    public function addAgentMaintenance(AgentMaintenance $agentMaintenance): self
    {
        if (!$this->agentMaintenances->contains($agentMaintenance)) {
            $this->agentMaintenances[] = $agentMaintenance;
            $agentMaintenance->addMesPole($this);
        }

        return $this;
    }

    public function removeAgentMaintenance(AgentMaintenance $agentMaintenance): self
    {
        if ($this->agentMaintenances->removeElement($agentMaintenance)) {
            $agentMaintenance->removeMesPole($this);
        }

        return $this;
    }

    public function getChefPole(): ?ChefPole
    {
        return $this->chefPole;
    }

    public function setChefPole(ChefPole $chefPole): self
    {
        // set the owning side of the relation if necessary
        if ($chefPole->getMonPole() !== $this) {
            $chefPole->setMonPole($this);
        }

        $this->chefPole = $chefPole;

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
            $demandeIntervention->setPoleConcerne($this);
        }

        return $this;
    }

    public function removeDemandeIntervention(DemandeIntervention $demandeIntervention): self
    {
        if ($this->demandeInterventions->removeElement($demandeIntervention)) {
            // set the owning side to null (unless already changed)
            if ($demandeIntervention->getPoleConcerne() === $this) {
                $demandeIntervention->setPoleConcerne(null);
            }
        }

        return $this;
    }
}
