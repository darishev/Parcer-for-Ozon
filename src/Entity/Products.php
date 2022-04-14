<?php

namespace App\Entity;

use App\Repository\ProductsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductsRepository::class)
 */
class Products
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $sku;

    /**
     * @ORM\Column(type="integer")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $reviews_count;

    /**
     * @ORM\Column(type="integer")
     */
    private $created_date;

    /**
     * @ORM\Column(type="integer")
     */
    private $updated_date;

    /**
     * @ORM\ManyToOne(targetEntity=Seller::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $seller;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): ?int
    {
        return $this->sku;
    }

    public function setSku(int $sku): self
    {
        $this->sku = $sku;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

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

    public function getReviewsCount(): ?int
    {
        return $this->reviews_count;
    }

    public function setReviewsCount(?int $reviews_count): self
    {
        $this->reviews_count = $reviews_count;

        return $this;
    }

    public function getCreatedDate(): ?int
    {
        return $this->created_date;
    }

    public function setCreatedDate(int $created_date): self
    {
        $this->created_date = $created_date;

        return $this;
    }

    public function getUpdatedDate(): ?int
    {
        return $this->updated_date;
    }

    public function setUpdatedDate(int $updated_date): self
    {
        $this->updated_date = $updated_date;

        return $this;
    }

    public function getSeller(): ?Seller
    {
        return $this->seller;
    }

    public function setSeller(?Seller $seller): self
    {
        $this->seller = $seller;

        return $this;
    }
}
