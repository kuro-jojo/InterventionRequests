<?php

namespace App\Entity;

use App\Entity\Responsable;
use App\DBAL\Types\Priorite;
use Doctrine\ORM\Mapping as ORM;
use App\DBAL\Types\DepartementType;
use App\DBAL\Types\CauseDefaillanceType;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\DemandeInterventionRepository;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * @ORM\Entity(repositoryClass=DemandeInterventionRepository::class)
 */
class DemandeIntervention
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nomDemandeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $emailDemandeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $contactDemandeur;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $fonction;

    /**
     * @ORM\ManyToMany(targetEntity=Responsable::class, mappedBy="demandeInterventionsSuivies")
     */
    private $responsables;

    /**
     * @ORM\ManyToOne(targetEntity=Pole::class, inversedBy="demandeInterventions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $poleConcerne;

    /**
     * @ORM\ManyToMany(targetEntity=AgentMaintenance::class, inversedBy="demandeInterventions")
     */
    private $traiteursDemande;

    /**
     * @ORM\Column(name="priorite", type="Priorite", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\Priorite")
     */
    private $priorite;

    /**
     * @ORM\Column(name="department", type="DepartementType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\DepartementType")
     */
    private $department;

    /**
     * @ORM\Column(name="causeDefaillance", type="CauseDefaillanceType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\CauseDefaillanceType")
     */
    private $causeDefaillance;

    public function __construct()
    {
        $this->responsables = new ArrayCollection();
        $this->traiteursDemande = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomDemandeur(): ?string
    {
        return $this->nomDemandeur;
    }

    public function setNomDemandeur(string $nomDemandeur): self
    {
        $this->nomDemandeur = $nomDemandeur;

        return $this;
    }

    public function getEmailDemandeur(): ?string
    {
        return $this->emailDemandeur;
    }

    public function setEmailDemandeur(string $emailDemandeur): self
    {
        $this->emailDemandeur = $emailDemandeur;

        return $this;
    }

    public function getContactDemandeur(): ?string
    {
        return $this->contactDemandeur;
    }

    public function setContactDemandeur(string $contactDemandeur): self
    {
        $this->contactDemandeur = $contactDemandeur;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getPriorite()
    {
        return $this->priorite;
    }

    /**
     * @param mixed $priorite
     * @return DemandeIntervention
     */
    public function setPriorite($priorite)
    {
        $this->priorite = $priorite;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDepartment()
    {
        return $this->department;
    }

    /**
     * @param mixed $department
     * @return DemandeIntervention
     */
    public function setDepartment($department)
    {
        $this->department = $department;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCauseDefaillance()
    {
        return $this->causeDefaillance;
    }

    /**
     * @param mixed $causeDefaillance
     * @return DemandeIntervention
     */
    public function setCauseDefaillance($causeDefaillance)
    {
        $this->causeDefaillance = $causeDefaillance;
        return $this;
    }



    public function getFonction(): ?string
    {
        return $this->fonction;
    }

    public function setFonction(string $fonction): self
    {
        $this->fonction = $fonction;

        return $this;
    }

    /**
     * @return Collection|Responsable[]
     */
    public function getResponsables(): Collection
    {
        return $this->responsables;
    }

    public function addResponsable(Responsable $responsable): self
    {
        if (!$this->responsables->contains($responsable)) {
            $this->responsables[] = $responsable;
            $responsable->addDemandeIntervention($this);
        }

        return $this;
    }

    public function removeResponsable(Responsable $responsable): self
    {
        if ($this->responsables->removeElement($responsable)) {
            $responsable->removeDemandeIntervention($this);
        }

        return $this;
    }

    public function getPoleConcerne(): ?Pole
    {
        return $this->poleConcerne;
    }

    public function setPoleConcerne(?Pole $poleConcerne): self
    {
        $this->poleConcerne = $poleConcerne;

        return $this;
    }

    /**
     * @return Collection|AgentMaintenance[]
     */
    public function getTraiteursDemande(): Collection
    {
        return $this->traiteursDemande;
    }

    public function addTraiteursDemande(AgentMaintenance $traiteursDemande): self
    {
        if (!$this->traiteursDemande->contains($traiteursDemande)) {
            $this->traiteursDemande[] = $traiteursDemande;
        }

        return $this;
    }

    public function removeTraiteursDemande(AgentMaintenance $traiteursDemande): self
    {
        $this->traiteursDemande->removeElement($traiteursDemande);

        return $this;
    }
}
