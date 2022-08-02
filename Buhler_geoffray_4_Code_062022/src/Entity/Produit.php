<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProduitRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProduitRepository::class)]
#[ApiResource(
    attributes:[
        "order" => ['price'=>'ASC'],
        "pagination_maximum_items_per_page" => 10
    ],
    collectionOperations:[
        "GET"
    ],
    itemOperations:[
        "GET"
    ],
)]
class Produit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $Name;

    #[ORM\Column(type: 'string', length: 25)]
    private $Color;

    #[ORM\Column(type: 'text')]
    private $Description;

    #[ORM\Column(type: 'integer')]
    private $Prices;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): self
    {
        $this->Name = $Name;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->Color;
    }

    public function setColor(string $Color): self
    {
        $this->Color = $Color;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): self
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPrices(): ?int
    {
        return $this->Prices;
    }

    public function setPrices(int $Prices): self
    {
        $this->Prices = $Prices;

        return $this;
    }
}
