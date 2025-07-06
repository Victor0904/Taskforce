<?php

namespace App\Entity;

use App\Repository\CollaborateurCompetenceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CollaborateurCompetenceRepository::class)]
class CollaborateurCompetence
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['collabcomp:read', 'collab:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'competences')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Collaborateur $collaborateur = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['collabcomp:read', 'collabcomp:write', 'collab:read'])]
    private ?Competence $competence = null;

    #[ORM\Column]
    #[Groups(['collabcomp:read', 'collabcomp:write', 'collab:read'])]
    private ?int $niveau = null;   // 0 → 10

    // ─────────── Getters / setters ───────────

    public function getId(): ?int { return $this->id; }

    public function getCollaborateur(): ?Collaborateur { return $this->collaborateur; }
    public function setCollaborateur(?Collaborateur $c): static
    {
        $this->collaborateur = $c;
        return $this;
    }

    public function getCompetence(): ?Competence { return $this->competence; }
    public function setCompetence(?Competence $c): static
    {
        $this->competence = $c;
        return $this;
    }

    public function getNiveau(): ?int { return $this->niveau; }
    public function setNiveau(int $n): static
    {
        $this->niveau = $n;
        return $this;
    }
}
