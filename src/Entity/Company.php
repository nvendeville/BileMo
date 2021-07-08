<?php

namespace App\Entity;

use App\Repository\CompanyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *          "api_delete_company",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route(
 *          "api_update_company",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *   "get a company",
 *   href = @Hateoas\Route(
 *        "api_get_company",
 *        parameters = { "id" = "expr(object.getId())" },
 *        absolute = true
 *   )
 * )
 * @Hateoas\Relation(
 *   "get all companies",
 *   href = @Hateoas\Route(
 *        "api_get_companies",
 *        absolute = true
 *   )
 * )
 * @Hateoas\Relation(
 *   "create",
 *   href = @Hateoas\Route(
 *        "api_create_company",
 *        parameters = { "id" = "expr(object.getId())" },
 *        absolute = true
 *   )
 * )
 */
#[ORM\Entity(repositoryClass: CompanyRepository::class)]
#[UniqueEntity('siret')]
#[JMS\ExclusionPolicy(['all'])]
class Company
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[JMS\Expose]
    private ?int $id;

    #[ORM\Column(length: 255)]
    #[JMS\Expose]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(length: 255)]
    #[JMS\Expose]
    private ?string $address;

    #[ORM\Column(length: 255)]
    #[JMS\Expose]
    #[Assert\NotBlank]
    private string $siret;

    #[ORM\OneToMany(
        mappedBy: 'company',
        targetEntity: 'User',
        orphanRemoval: true
    )]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getName(): string
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

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getSiret(): string
    {
        return $this->siret;
    }

    public function setSiret(string $siret): self
    {
        $this->siret = $siret;

        return $this;
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): self
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
            $user->setCompany($this);
        }

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
