<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\DemandeIntervention;
use App\Repository\ResponsableRepository;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity(repositoryClass=ResponsableRepository::class)
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type",type="string")
 * @ORM\DiscriminatorMap({"chefPole"="ChefPole","chefService"="ChefService"})
 */
abstract class Responsable extends User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=DemandeIntervention::class, inversedBy="responsables")
     */
    private $demandeInterventionsSuivies;

    public function __construct()
    {
        $this->demandeInterventionsSuivies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|DemandeIntervention[]
     */
    public function getDemandeInterventionsSuivies(): ?Collection
    {
        return $this->demandeInterventionsSuivies;
    }

    public function addDemandeIntervention(?DemandeIntervention $demandeIntervention): self
    {
        if (!$this->demandeInterventionsSuivies->contains($demandeIntervention)) {
            $this->demandeInterventionsSuivies[] = $demandeIntervention;
        }

        return $this;
    }

    public function removeDemandeIntervention(DemandeIntervention $demandeIntervention): self
    {
        $this->demandeInterventionsSuivies->removeElement($demandeIntervention);

        return $this;
    }
}
