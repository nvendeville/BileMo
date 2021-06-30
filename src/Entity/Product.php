<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;


/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 * @UniqueEntity("reference")
 * @JMS\ExclusionPolicy("ALL")
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *          "api_delete_product",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     ),
 *      exclusion = @Hateoas\Exclusion(
 *          excludeIf = "expr(not is_granted('ROLE_ADMIN'))"
 *      )
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route(
 *          "api_update_product",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @JMS\Expose
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @JMS\Expose
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     * @JMS\Expose
     * @Assert\NotBlank
     */
    private $reference;

    /**
     * @ORM\Column(type="datetime")
     */
    private $added;

    /**
     * @ORM\Column(type="datetime")
     */
    private $updated;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @JMS\Expose
     */
    private $price;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function setReference(string $reference): self
    {
        $this->reference = $reference;

        return $this;
    }

    public function getAdded(): ?\DateTimeInterface
    {
        return $this->added;
    }

    public function setAdded(\DateTimeInterface $added): self
    {
        $this->added = $added;

        return $this;
    }

    public function getUpdated(): ?\DateTimeInterface
    {
        return $this->updated;
    }

    public function setUpdated(\DateTimeInterface $updated): self
    {
        $this->updated = $updated;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }
}
