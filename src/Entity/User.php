<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**

 * @Hateoas\Relation(
 *     "get all users",
 *     href = @Hateoas\Route(
 *          "api_get_users",
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "get a user",
 *     href = @Hateoas\Route(
 *          "api_get_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *          "api_delete_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route(
 *          "api_update_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route(
 *          "api_create_user",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * @JMS\ExclusionPolicy("ALL")
 */
#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity('email')]
#[JMS\ExclusionPolicy(['all'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    public const SUPERADMIN = 'ROLE_SUPER_ADMIN';
    public const ADMIN = 'ROLE_ADMIN';
    public const USER = 'ROLE_USER';

    /**
     * @JMS\Expose
     */
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private int $id;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(length: 180)]
    #[Assert\NotBlank]
    private string $email;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /**
     * @JMS\Expose
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $password;

    #[ORM\ManyToOne(
        targetEntity: 'Company',
        inversedBy: 'users'
    )]
    #[ORM\JoinColumn(nullable: false)]
    private Company $company;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getEmail(): string
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
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
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

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function setCompany(Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
