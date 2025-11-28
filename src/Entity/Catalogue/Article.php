<?php

namespace App\Entity\Catalogue;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\InheritanceType("SINGLE_TABLE")]
#[ORM\DiscriminatorColumn(name: "article_type", type: "string")]
#[ORM\DiscriminatorMap(["article" => "Article", "livre" => "Livre", "musique" => "Musique", "VideoGame" => "VideoGame", "film" => "Film"])]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(length: 255, name: 'titre')]
    private ?string $titre = null;

    #[ORM\Column(name: 'disponibilite')]
    private ?int $disponibilite = null;
	
    #[ORM\Column(length: 255, name: 'image')]
    private ?string $image = null;
	
    #[ORM\Column(length: 255, name: 'category')]
    private ?string $category = "Inconnu";

    #[ORM\Column(name: 'prix')]
    private ?float $prix = null;
	
    #[ORM\Column(name: 'shippingPrice')]
    private ?float $shippingPrice = 0;


    public function getId(): ?int
    {
        return $this->id;
    }
	
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setCategory(string $category): static
    {
        $this->category = $category;
        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;

        return $this;
    }

    public function getDisponibilite(): ?int
    {
        return $this->disponibilite;
    }

    public function setDisponibilite(int $disponibilite): static
    {
        $this->disponibilite = $disponibilite;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getPrice(): ?string
    {
        return $this->price;
    }

    public function setPrice(string $price): static
    {
        $this->price = $price;

        return $this;
    }

    public function getShippingPrice(): ?string
    {
        return $this->shippingPrice;
    }

    public function setShippingPrice(string $shippingPrice): static
    {
        $this->shippingPrice = $shippingPrice;

        return $this;
    }
}

