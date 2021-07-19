<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Hateoas\Configuration\Annotation as Hateoas;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @Hateoas\Relation(
 *     "delete",
 *     href = @Hateoas\Route(
 *          "api_delete_product",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "update",
 *     href = @Hateoas\Route(
 *          "api_update_product",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "get all products",
 *     href = @Hateoas\Route(
 *          "api_get_products",
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "get one product",
 *     href = @Hateoas\Route(
 *          "api_get_products",
 *          parameters = { "id" = "expr(object.getId())" },
 *          absolute = true
 *     )
 * )
 * @Hateoas\Relation(
 *     "create",
 *     href = @Hateoas\Route(
 *          "api_create_product",
 *          absolute = true
 *     )
 * )
 * @JMS\ExclusionPolicy("ALL")
 */
#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[UniqueEntity('reference')]
class Product
{
    /**
     * @JMS\Expose
     */
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(length: 255)]
    private ?string $name;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(length: 255)]
    private ?string $description;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $reference;

    #[ORM\Column(type: 'datetime')]
    private DateTime $added;

    #[ORM\Column(type: 'datetime')]
    private DateTime $updated;

    /**
     * @JMS\Expose
     */
    #[ORM\Column(type: 'float')]
    private ?float $price;

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
