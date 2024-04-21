<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 1000)]
    private ?string $Description = null;

    #[ORM\Column]
    private ?float $PriceHT = null;

    #[ORM\Column]
    private ?bool $Available = null;

    #[ORM\OneToMany(targetEntity: CartLine::class, mappedBy: 'product')]
    private Collection $CartLine;

    #[ORM\OneToMany(targetEntity: CommandLine::class, mappedBy: 'product')]
    private ?Collection $CommandLine ;

    #[ORM\ManyToOne(inversedBy: 'products')]
    #[ORM\JoinColumn(nullable: true)]
    private ?Category $Category = null;

    #[ORM\OneToMany(targetEntity: Media::class, mappedBy: 'product')]
    private Collection $Media;

    public function __construct()
    {
        $this->CartLine = new ArrayCollection();
        $this->CommandLine = new ArrayCollection();
        $this->Media = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->Name;
    }

    public function setName(string $Name): static
    {
        $this->Name = $Name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->Description;
    }

    public function setDescription(string $Description): static
    {
        $this->Description = $Description;

        return $this;
    }

    public function getPriceHT(): ?int
    {
        return $this->PriceHT;
    }

    public function setPriceHT(int $PriceHT): static
    {
        $this->PriceHT = $PriceHT;

        return $this;
    }

    public function isAvailable(): ?bool
    {
        return $this->Available;
    }

    public function setAvailable(bool $Available): static
    {
        $this->Available = $Available;

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
            $cartLine->setProduct($this);
        }

        return $this;
    }

    public function removeCartLine(CartLine $cartLine): static
    {
        if ($this->CartLine->removeElement($cartLine)) {
            // set the owning side to null (unless already changed)
            if ($cartLine->getProduct() === $this) {
                $cartLine->setProduct(null);
            }
        }

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
            $commandLine->setProduct($this);
        }

        return $this;
    }

    public function removeCommandLine(CommandLine $commandLine): static
    {
        if ($this->CommandLine->removeElement($commandLine)) {
            // set the owning side to null (unless already changed)
            if ($commandLine->getProduct() === $this) {
                $commandLine->setProduct(null);
            }
        }

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->Category;
    }

    public function setCategory(?Category $Category): static
    {
        $this->Category = $Category;

        return $this;
    }

    /**
     * @return Collection<int, Media>
     */
    public function getMedia(): Collection
    {
        return $this->Media;
    }

    public function addMedium(Media $medium): static
    {
        if (!$this->Media->contains($medium)) {
            $this->Media->add($medium);
            $medium->setProduct($this);
        }

        return $this;
    }

    public function removeMedium(Media $medium): static
    {
        if ($this->Media->removeElement($medium)) {
            // set the owning side to null (unless already changed)
            if ($medium->getProduct() === $this) {
                $medium->setProduct(null);
            }
        }

        return $this;
    }
}
