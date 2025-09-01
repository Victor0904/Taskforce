<?php

namespace App\Entity;

use App\Repository\CollaborateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CollaborateurRepository::class)]
class Collaborateur
{
    public function __construct()
    {
        // $this->createdAt   = new \DateTimeImmutable();
        $this->competences = new ArrayCollection();

    }

    // ─────────── Champs simples ───────────

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'collab:read', 'mission:read', 'tache:read'
    ])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'collab:read', 'collab:write', 'mission:read', 'tache:read'
    ])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'collab:read', 'collab:write', 'mission:read', 'tache:read'
    ])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255, unique: true)]
    #[Groups(['collab:read', 'collab:write', 'mission:read'])]
    private ?string $email = null;

    #[ORM\Column(length: 50)]
    #[Groups(['collab:read', 'collab:write'])]
    #[Assert\NotBlank]
    #[Assert\Choice(choices: ['Collaborateur', 'Manager', 'Chef de projet'])]
    private ?string $role = null;

    #[ORM\Column(type: 'float')]
    #[Groups(['collab:read', 'collab:write'])]
    private ?float $chargeActuelle = 0;

    #[ORM\Column(type: 'boolean')]
    #[Groups(['collab:read', 'collab:write'])]
    private ?bool $disponible = true;

    // #[ORM\Column]
    // #[Groups(['collab:read'])]
    // private ?\DateTimeImmutable $createdAt = null;

    // ─────────── Relations ───────────

    #[ORM\OneToMany(
        mappedBy: 'collaborateur',
        targetEntity: CollaborateurCompetence::class,
        orphanRemoval: true,
        cascade: ['persist', 'remove']
    )]
    #[Groups(['collab:read'])] // ❌ pas exposé dans tache:read pour garder les tâches légères
    private Collection $competences;



    // ─────────── Getters / setters ───────────

    public function getId(): ?int { return $this->id; }

    public function getNom(): ?string { return $this->nom; }
    public function setNom(string $nom): static { $this->nom = $nom; return $this; }

    public function getPrenom(): ?string { return $this->prenom; }
    public function setPrenom(string $prenom): static { $this->prenom = $prenom; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getRole(): ?string { return $this->role; }
    public function setRole(string $role): static { $this->role = $role; return $this; }

    public function getChargeActuelle(): ?float { return $this->chargeActuelle; }
    public function setChargeActuelle(float $c): static { $this->chargeActuelle = $c; return $this; }

    public function isDisponible(): ?bool { return $this->disponible; }
    public function setDisponible(bool $d): static { $this->disponible = $d; return $this; }

    // public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    /** @return Collection<int, CollaborateurCompetence> */
    public function getCompetences(): Collection { return $this->competences; }

    public function addCompetence(CollaborateurCompetence $link): static
    {
        if (!$this->competences->contains($link)) {
            $this->competences[] = $link;
            $link->setCollaborateur($this);
        }
        return $this;
    }

    public function removeCompetence(CollaborateurCompetence $link): static
    {
        if ($this->competences->removeElement($link) && $link->getCollaborateur() === $this) {
            $link->setCollaborateur(null);
        }
        return $this;
    }


}
