<?php

namespace App\Entity;

use App\Repository\HistoriqueTacheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Tache;
use App\Entity\Collaborateur;


#[ORM\Entity(repositoryClass: HistoriqueTacheRepository::class)]
class HistoriqueTache
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Tache $tache = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collaborateur $ancienCollab = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collaborateur $nouveauCollab = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateReaffectation = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $raison = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTache(): ?Tache
    {
        return $this->tache;
    }

    public function setTache(?Tache $tache): static
    {
        $this->tache = $tache;
        return $this;
    }

    public function getAncienCollab(): ?Collaborateur
    {
        return $this->ancienCollab;
    }

    public function setAncienCollab(?Collaborateur $ancienCollab): static
    {
        $this->ancienCollab = $ancienCollab;
        return $this;
    }

    public function getNouveauCollab(): ?Collaborateur
    {
        return $this->nouveauCollab;
    }

    public function setNouveauCollab(?Collaborateur $nouveauCollab): static
    {
        $this->nouveauCollab = $nouveauCollab;
        return $this;
    }

    public function getDateReaffectation(): ?\DateTimeInterface
    {
        return $this->dateReaffectation;
    }

    public function setDateReaffectation(\DateTimeInterface $dateReaffectation): static
    {
        $this->dateReaffectation = $dateReaffectation;
        return $this;
    }

    public function getRaison(): ?string
    {
        return $this->raison;
    }

    public function setRaison(string $raison): static
    {
        $this->raison = $raison;
        return $this;
    }
}
