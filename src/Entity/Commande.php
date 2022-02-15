<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $dateCm;

    #[ORM\OneToOne(targetEntity: Panier::class, cascade: ['persist', 'remove'])]
    private $panier;

    public $fullName;
    public $numeroTel;
    public $adresse;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCm(): ?\DateTimeInterface
    {
        return $this->dateCm;
    }

    public function setDateCm(\DateTimeInterface $dateCm): self
    {
        $this->dateCm = $dateCm;

        return $this;
    }

    public function getPanier(): ?Panier
    {
        return $this->panier;
    }

    public function setPanier(?Panier $panier): self
    {
        $this->panier = $panier;

        return $this;
    }
}
