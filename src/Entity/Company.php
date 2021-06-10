<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CompanyRepository::class)]
class Company
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['api_get'])]
    private int $id;

    #[ORM\Column(length: 255)]
    #[Groups(['api_get'])]
    private string $name;

    #[ORM\Column(
        length: 255,
        nullable: true
    )]
    #[Groups(['api_get'])]
    private ?string $address;

    #[ORM\Column(
        length: 255,
        nullable: true
    )]
    #[Groups(['api_get'])]
    private ?string $siret;

    #[ORM\OneToMany(
        mappedBy: "company",
        targetEntity: User::class,
        orphanRemoval: true
    )]
    private Collection $users;

    #[Pure]
    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getSiret(): ?string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if ($this->users == null) {
            $this->users[] = $user;
            $user->setCompany($this);
        }
        /*
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCompany($this);
        }*/

        return $this;
    }

    public function removeUser(User $user): self
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getCompany() === $this) {
                $user->setCompany(null);
            }
        }

        return $this;
    }
}
