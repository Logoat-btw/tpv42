<?php


namespace App\Entity\Panier;


use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private \DateTimeInterface $date;

    #[ORM\Column(type: 'string', length: 255)]
    private string $pays;

    #[ORM\Column(type: 'string', length: 255)]
    private string $ville;

    #[ORM\Column(type: 'string', length: 255)]
    private string $adresse;

    #[ORM\Column(type: 'string', length: 20)]
    private string $codePostal;

    #[ORM\Column(type: 'string', length: 255)]
    private string $paiement;

    #[ORM\Column(type: 'string', length: 20)]
    private string $telephone;

    #[ORM\Column(type: 'json')]
    private array $articles = [];

    #[ORM\Column(type: 'float')]
    private float $total;

    public function __construct()
    {
        $this->date = new \DateTime();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getDate(): \DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getTotal(): float
    {
        return $this->total;
    }

    public function setTotal(float $total): void
    {
        $this->total = $total;
    }

    public function getArticles(): array
    {
        return $this->articles;
    }
    
    public function setArticles(array $articles): void
    {
        $this->articles = $articles;
    }
    

    public function getAdresse(): string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): void
    {
        $this->adresse = $adresse;
    }

    public function getVille(): string
    {
        return $this->ville;
    }

    public function setVille(string $ville): void
    {
        $this->ville = $ville;
    }

    public function getPays(): string
    {
        return $this->pays;
    }

    public function setPays(string $pays): void
    {
        $this->pays = $pays;
    }

    public function getCodePostal(): string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): void
    {
        $this->codePostal = $codePostal;
    }

    public function getPaiement(): string
{
    return $this->paiement;
}

public function setPaiement(string $paiement): void
{
    $this->paiement = $paiement;
}
public function getTelephone(): string
{
    return $this->telephone;
}

public function setTelephone(string $telephone): void
{
    $this->telephone = $telephone;
}

}
