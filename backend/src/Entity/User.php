<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email', message: 'Cette adresse email est déjà utilisée.')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /* ----------  CORE  ---------- */

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /** @var string Hashed password */
    #[ORM\Column]
    private string $password = '';

    /* ----------  GETTERS / SETTERS  ---------- */

    public function getId(): ?int
    {
        return $this->id;
    }

    /* ---- Email / identifier ---- */

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /** Identifiant unique utilisé par Symfony Security */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /* ---- Rôles ---- */

    /**
     * @return list<string>
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';   // rôle minimal garanti

        return array_unique($roles);
    }

    /**
     * @param list<string> $roles
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function addRole(string $role): self
    {
        $role = strtoupper($role);
        if (!in_array($role, $this->roles, true)) {
            $this->roles[] = $role;
        }

        return $this;
    }

    public function removeRole(string $role): self
    {
        $this->roles = array_values(array_diff($this->roles, [strtoupper($role)]));

        return $this;
    }

    /* ---- Password ---- */

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $hash): self
    {
        $this->password = $hash;

        return $this;
    }

    /* ---- UserInterface ---- */

    public function eraseCredentials(): void
    {
        // Si tu ajoutes un plainPassword, c’est ici qu’il faudra le remettre à null
    }

    /* ---- Utilitaire ---- */

    public function __toString(): string
    {
        return $this->email ?? '';
    }
}
