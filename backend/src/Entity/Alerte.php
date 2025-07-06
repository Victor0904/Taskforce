<?php

namespace App\Entity;

use App\Repository\AlerteRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: AlerteRepository::class)]
class Alerte
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['alerte:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Groups(['alerte:read', 'alerte:write'])]
    private ?string $type = null; // 'surcharge', 'retard', 'inactivité'

    #[ORM\Column(length: 255)]
    #[Groups(['alerte:read', 'alerte:write'])]
    private ?string $message = null;

    #[ORM\Column]
    #[Groups(['alerte:read', 'alerte:write'])]
    private ?\DateTimeImmutable $dateAlerte = null;

    #[ORM\Column]
    #[Groups(['alerte:read', 'alerte:write'])]
    private ?bool $resolue = false;

    #[ORM\ManyToOne(inversedBy: 'alertes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['alerte:read', 'alerte:write'])]
    private ?Collaborateur $collaborateur = null;

    public function __construct()
    {
        $this->dateAlerte = new \DateTimeImmutable();
        $this->resolue = false;
    }

    public function getId(): ?int { return $this->id; }

    public function getType(): ?string { return $this->type; }
    public function setType(string $type): static { $this->type = $type; return $this; }

    public function getMessage(): ?string { return $this->message; }
    public function setMessage(string $message): static { $this->message = $message; return $this; }

    public function getDateAlerte(): ?\DateTimeImmutable { return $this->dateAlerte; }
    public function setDateAlerte(\DateTimeImmutable $date): static { $this->dateAlerte = $date; return $this; }

    public function isResolue(): ?bool { return $this->resolue; }
    public function setResolue(bool $resolue): static { $this->resolue = $resolue; return $this; }

    public function getCollaborateur(): ?Collaborateur { return $this->collaborateur; }
    public function setCollaborateur(?Collaborateur $collaborateur): static { $this->collaborateur = $collaborateur; return $this; }
}
