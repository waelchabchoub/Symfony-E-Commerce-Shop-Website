<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'date')]
    private $datePn;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'paniers')]
    private $user;

    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'paniers')]
    private $articles;

    public function __construct()
    {
        $this->articles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDatePn(): ?\DateTimeInterface
    {
        return $this->datePn;
    }

    public function setDatePn(\DateTimeInterface $datePn): self
    {
        $this->datePn = $datePn;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getArticles(): Collection
    {
        return $this->articles;
    }

    public function addArticle(Product $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
        }

        return $this;
    }

    public function removeArticle(Product $article): self
    {
        $this->articles->removeElement($article);

        return $this;
    }
}
