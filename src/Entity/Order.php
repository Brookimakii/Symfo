<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $OrderNumber = null;

    #[ORM\Column]
    private ?bool $Valid = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $DateTime = null;

    #[ORM\ManyToOne(inversedBy: 'PlacedOrder')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'cartOrder', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Cart $Cart = null;

    #[ORM\OneToMany(targetEntity: CommandLine::class, mappedBy: 'orderQuantity')]
    private Collection $CommandLine;

    public function __construct()
    {
        $this->CommandLine = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): ?int
    {
        return $this->OrderNumber;
    }

    public function setOrderNumber(int $OrderNumber): static
    {
        $this->OrderNumber = $OrderNumber;

        return $this;
    }

    public function isValid(): ?bool
    {
        return $this->Valid;
    }

    public function setValid(bool $Valid): static
    {
        $this->Valid = $Valid;

        return $this;
    }

    public function getDateTime(): ?\DateTimeInterface
    {
        return $this->DateTime;
    }

    public function setDateTime(\DateTimeInterface $DateTime): static
    {
        $this->DateTime = $DateTime;

        return $this;
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

    public function getCart(): ?Cart
    {
        return $this->Cart;
    }

    public function setCart(Cart $Cart): static
    {
        $this->Cart = $Cart;

        return $this;
    }

    /**
     * @return Collection<int, CommandLine>
     */
    public function getCommandLine(): Collection
    {
        return $this->CommandLine;
    }

    public function addCommandLine(CommandLine $commandLine): static
    {
        if (!$this->CommandLine->contains($commandLine)) {
            $this->CommandLine->add($commandLine);
            $commandLine->setOrderQuantity($this);
        }

        return $this;
    }

    public function removeCommandLine(CommandLine $commandLine): static
    {
        if ($this->CommandLine->removeElement($commandLine)) {
            // set the owning side to null (unless already changed)
            if ($commandLine->getOrderQuantity() === $this) {
                $commandLine->setOrderQuantity(null);
            }
        }

        return $this;
    }

	public function updateCommandLine(CartLine $cartLine) {


	}
	public function getOrderLineByProduct(Product $product): ?CommandLine
	{
		foreach ($this->getCommandLine() as $commandLine) {
			if ($commandLine->getProduct() === $product) {
				return $commandLine;
			}
		}

		return null;
	}
}
