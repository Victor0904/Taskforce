<?php

namespace App\Entity;

use App\Repository\MissionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Collaborateur;
use App\Entity\Tache;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MissionRepository::class)]
class Mission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['mission:read', 'tache:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['mission:read', 'mission:write', 'tache:read', 'tache:write'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['mission:read', 'mission:write', 'tache:read'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['mission:read', 'mission:write', 'tache:read'])]
    private ?int $priorite = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['mission:read', 'mission:write', 'tache:read'])]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['mission:read', 'mission:write', 'tache:read'])]
    private ?\DateTimeInterface $dateFinPrevue = null;

    #[ORM\Column(length: 255)]
    #[Groups(['mission:read', 'mission:write', 'tache:read'])]
    private ?string $statut = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['mission:read', 'mission:write', 'tache:read'])]
    private ?Collaborateur $responsable = null;

    #[ORM\OneToMany(mappedBy: 'mission', targetEntity: Tache::class, cascade: ['remove'], orphanRemoval: true)]
    #[Groups(['mission:read'])] // On peut aussi ajouter 'tache:read' si tu veux les voir côté tâche
    private Collection $taches;

    public function __construct()
    {
        $this->taches = new ArrayCollection();
    }

    // ──────── Getters / Setters ─────────

    public function getId(): ?int { return $this->id; }

    public function getTitre(): ?string { return $this->titre; }
    public function setTitre(string $titre): static { $this->titre = $titre; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getPriorite(): ?int { return $this->priorite; }
    public function setPriorite(int $priorite): static { $this->priorite = $priorite; return $this; }

    public function getDateDebut(): ?\DateTimeInterface { return $this->dateDebut; }
    public function setDateDebut(\DateTimeInterface $dateDebut): static { $this->dateDebut = $dateDebut; return $this; }

    public function getDateFinPrevue(): ?\DateTimeInterface { return $this->dateFinPrevue; }
    public function setDateFinPrevue(\DateTimeInterface $dateFinPrevue): static { $this->dateFinPrevue = $dateFinPrevue; return $this; }

    public function getStatut(): ?string { return $this->statut; }
    public function setStatut(string $statut): static { $this->statut = $statut; return $this; }

    public function getResponsable(): ?Collaborateur { return $this->responsable; }
    public function setResponsable(?Collaborateur $responsable): static { $this->responsable = $responsable; return $this; }

    /** @return Collection<int, Tache> */
    public function getTaches(): Collection
    {
        return $this->taches;
    }

    public function addTache(Tache $tache): static
    {
        if (!$this->taches->contains($tache)) {
            $this->taches[] = $tache;
            $tache->setMission($this);
        }
        return $this;
    }

    public function removeTache(Tache $tache): static
    {
        if ($this->taches->removeElement($tache)) {
            if ($tache->getMission() === $this) {
                $tache->setMission(null);
            }
        }
        return $this;
    }
}
