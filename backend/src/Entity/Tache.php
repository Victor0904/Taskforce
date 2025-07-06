<?php

namespace App\Entity;

use App\Repository\TacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Mission;
use App\Entity\Collaborateur;
use App\Entity\Competence;
use Symfony\Component\Serializer\Annotation\Groups;




#[ORM\Entity(repositoryClass: TacheRepository::class)]
class Tache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tache:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?string $titre = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?string $description = null;

    #[ORM\Column]
    #[Groups(['tache:read', 'tache:write'])]
    private ?float $chargeEstimee = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?float $chargeReelle = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?\DateTimeInterface $dateDebut = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?\DateTimeInterface $dateFinPrevue = null;

    #[ORM\Column(length: 50)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?string $statut = null;

    #[ORM\Column]
    #[Groups(['tache:read', 'tache:write'])]
    private ?int $priorite = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?Mission $mission = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?Collaborateur $collaborateur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tache:read', 'tache:write'])]
    private ?Competence $competenceRequise = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;
        return $this;
    }

    public function getChargeEstimee(): ?float
    {
        return $this->chargeEstimee;
    }

    public function setChargeEstimee(float $chargeEstimee): static
    {
        $this->chargeEstimee = $chargeEstimee;
        return $this;
    }

    public function getChargeReelle(): ?float
    {
        return $this->chargeReelle;
    }

    public function setChargeReelle(?float $chargeReelle): static
    {
        $this->chargeReelle = $chargeReelle;
        return $this;
    }

    public function getDateDebut(): ?\DateTimeInterface
    {
        return $this->dateDebut;
    }

    public function setDateDebut(\DateTimeInterface $dateDebut): static
    {
        $this->dateDebut = $dateDebut;
        return $this;
    }

    public function getDateFinPrevue(): ?\DateTimeInterface
    {
        return $this->dateFinPrevue;
    }

    public function setDateFinPrevue(\DateTimeInterface $dateFinPrevue): static
    {
        $this->dateFinPrevue = $dateFinPrevue;
        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;
        return $this;
    }

    public function getPriorite(): ?int
    {
        return $this->priorite;
    }

    public function setPriorite(int $priorite): static
    {
        $this->priorite = $priorite;
        return $this;
    }

    public function getMission(): ?Mission
    {
        return $this->mission;
    }

    public function setMission(?Mission $mission): static
    {
        $this->mission = $mission;
        return $this;
    }

    public function getCollaborateur(): ?Collaborateur
    {
        return $this->collaborateur;
    }

    public function setCollaborateur(?Collaborateur $collaborateur): static
    {
        $this->collaborateur = $collaborateur;
        return $this;
    }

    public function getCompetenceRequise(): ?Competence
    {
        return $this->competenceRequise;
    }

    public function setCompetenceRequise(?Competence $competenceRequise): static
    {
        $this->competenceRequise = $competenceRequise;
        return $this;
    }
}
