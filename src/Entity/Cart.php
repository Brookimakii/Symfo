<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'Cart')]
    private ?User $user;

    #[ORM\OneToOne(mappedBy: 'Cart', cascade: ['persist', 'remove'])]
    private ?Order $cartOrder;

    #[ORM\OneToMany(targetEntity: CartLine::class, mappedBy: 'cart')]
    private Collection $CartLine;

    public function __construct()
    {
        $this->CartLine = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getCartOrder(): ?Order
    {
        return $this->cartOrder;
    }

    public function setCartOrder(Order $cartOrder): static
    {
        // set the owning side of the relation if necessary
        if ($cartOrder->getCart() !== $this) {
            $cartOrder->setCart($this);
        }

        $this->cartOrder = $cartOrder;

        return $this;
    }

    /**
     * @return Collection<int, CartLine>
     */
    public function getCartLine(): Collection
    {
        return $this->CartLine;
    }

    public function addCartLine(CartLine $cartLine): static
    {
        if (!$this->CartLine->contains($cartLine)) {
            $this->CartLine->add($cartLine);
            $cartLine->setCart($this);
        }

        return $this;
    }

    public function removeCartLine(CartLine $cartLine): static
    {
        if ($this->CartLine->removeElement($cartLine)) {
            // set the owning side to null (unless already changed)
            if ($cartLine->getCart() === $this) {
                $cartLine->setCart(null);
            }
        }

        return $this;
    }
}
