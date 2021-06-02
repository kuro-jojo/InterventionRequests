<?php

namespace App\Entity;

use App\Entity\Pole;
use App\Entity\Responsable;
use App\DBAL\Types\Priorite;
use App\DBAL\Types\StatutType;
use App\Entity\AgentMaintenance;
use Doctrine\ORM\Mapping as ORM;
use App\DBAL\Types\DepartementType;
use App\DBAL\Types\CauseDefaillanceType;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\DemandeInterventionRepository;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;
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
     * @Assert\NotBlank(message="Veuillez saisir votre nom")
     */
    private $nomDemandeur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Email(message="Adresse email incorrecte")
     * @Assert\NotBlank(message="Veuillez saisir une adresse email")
     * 
     */
    private $emailDemandeur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Veuillez entrez un numéro de contact")
     * @Assert\Regex(
     *      pattern="/^(00221)?(7[786])(\d){7}$/",
     *      message="Respectez le format 77 xxx xx xx "
     *          )
     * 
     */
    private $contactDemandeur;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Entrez votre fonction")
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
     * @ORM\Column(name="departement", type="DepartementType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\DepartementType")
     */
    private $departement;

    /**
     * @ORM\Column(name="causeDefaillance", type="CauseDefaillanceType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\CauseDefaillanceType")
     */
    private $causeDefaillance;

    /**
     * @Assert\Length(min="10",minMessage="La description doit faire au moins 10 caractères")
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateDemande;

    /**
     * @ORM\Column(type="StatutType")
     */
    private $statut;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateIntervention;

    /**
     * @ORM\Column(type="time", nullable=true)
     */
    private $dureeIntervention;

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
    public function getDepartement()
    {
        return $this->departement;
    }

    /**
     * @param mixed $departement
     * @return DemandeIntervention
     */
    public function setDepartement($department)
    {
        $this->departement = $department;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateDemande(): ?\DateTimeInterface
    {
        return $this->dateDemande;
    }

    public function setDateDemande(\DateTimeInterface $dateDemande): self
    {
        $this->dateDemande = $dateDemande;

        return $this;
    }

    public function getStatut()
    {
        return $this->statut;
    }

    public function setStatut($statut): self
    {
        $this->statut = $statut;

        return $this;
    }

    public function getDateIntervention(): ?\DateTimeInterface
    {
        return $this->dateIntervention;
    }

    public function setDateIntervention(?\DateTimeInterface $dateIntervention): self
    {
        $this->dateIntervention = $dateIntervention;

        return $this;
    }

    public function getDureeIntervention(): ?\DateTimeInterface
    {
        return $this->dureeIntervention;
    }

    public function setDureeIntervention(?\DateTimeInterface $dureeIntervention): self
    {
        $this->dureeIntervention = $dureeIntervention;

        return $this;
    }
}
