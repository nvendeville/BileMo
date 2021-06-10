<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['api_get'])]
    private int $id;

    #[ORM\Column(
        length: 180,
        unique: true
    )]
    #[Groups(['api_get'])]
    private string $email;

    #[ORM\Column(type: 'string')]
    #[Groups(['api_get'])]
    private string $roles;

    #[ORM\Column(length: 255)]
    #[Groups(['api_get'])]
    private string $password;

    #[ORM\Column(length: 255)]
    #[Groups(['api_get'])]
    private ?string $firstname;

    #[ORM\Column(length: 255)]
    #[Groups(['api_get'])]
    private string $lastname;

    #[ORM\Column(
        length: 255,
        unique: true
    )]
    #[Groups(['api_get'])]
    private ?string $phone;

    #[ORM\ManyToOne(
        targetEntity: Company::class,
        inversedBy: "users"
    )]
    #[ORM\Column(length: 255)]
    #[ORM\JoinColumn(nullable: false)]
    private Company $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): string
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        //$roles[] = 'ROLE_USER';
return $this->roles;
        //return array_unique($roles);
    }

    public function setRoles(string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
