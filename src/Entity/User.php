<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use OpenApi\Annotations as OA;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @UniqueEntity("email")
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
 * @JMS\ExclusionPolicy("ALL")
 */
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @JMS\Expose
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="string")
     * @JMS\Expose
     */
    private $roles;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank
     * @JMS\Expose
     */
    private $password;

    /**
     * @ORM\ManyToOne(targetEntity=Company::class, inversedBy="users")
     * @ORM\JoinColumn(nullable=false)
     * @JMS\Expose
     */
    private $company;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): string
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles = 'ROLE_USER';

        return $roles;
    }

    public function setRoles(string $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function setCompany(?Company $company): self
    {
        $this->company = $company;

        return $this;
    }
}
