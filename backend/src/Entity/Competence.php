<?php

namespace App\Entity;

use App\Repository\CompetenceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompetenceRepository::class)]
class Competence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['competence:read', 'collab:read', 'tache:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['competence:read', 'competence:write', 'collab:read', 'tache:read'])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['competence:read', 'competence:write', 'collab:read', 'tache:read'])]
    private ?string $description = null;

    // ─────────── getters / setters ───────────

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static
    {
        $this->nom = $nom;
        return $this;
    }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static
    {
        $this->description = $description;
        return $this;
    }
}
