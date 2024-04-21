<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $Name = null;

    #[ORM\Column(length: 255)]
    private ?string $FirstName = null;

    #[ORM\Column]
    private ?int $Phone = null;

    #[ORM\OneToMany(targetEntity: CustomerAddress::class, mappedBy: 'user')]
    private Collection $CustomerAddress;

    #[ORM\OneToMany(targetEntity: Cart::class, mappedBy: 'user')]
    private Collection $Cart;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user')]
    private Collection $PlacedOrder;

    public function __construct()
    {
        $this->CustomerAddress = new ArrayCollection();
        $this->Cart = new ArrayCollection();
        $this->PlacedOrder = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): static
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getPhone(): ?int
    {
        return $this->Phone;
    }

    public function setPhone(int $Phone): static
    {
        $this->Phone = $Phone;

        return $this;
    }

    /**
     * @return Collection<int, CustomerAddress>
     */
    public function getCustomerAddress(): Collection
    {
        return $this->CustomerAddress;
    }

    public function addCustomerAddress(CustomerAddress $customerAddress): static
    {
        if (!$this->CustomerAddress->contains($customerAddress)) {
            $this->CustomerAddress->add($customerAddress);
            $customerAddress->setUser($this);
        }

        return $this;
    }

    public function removeCustomerAddress(CustomerAddress $customerAddress): static
    {
        if ($this->CustomerAddress->removeElement($customerAddress)) {
            // set the owning side to null (unless already changed)
            if ($customerAddress->getUser() === $this) {
                $customerAddress->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Cart>
     */
    public function getCart(): Collection
    {
        return $this->Cart;
    }

    public function addCart(Cart $cart): static
    {
        if (!$this->Cart->contains($cart)) {
            $this->Cart->add($cart);
            $cart->setUser($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): static
    {
        if ($this->Cart->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getUser() === $this) {
                $cart->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Order>
     */
    public function getPlacedOrder(): Collection
    {
        return $this->PlacedOrder;
    }

    public function addPlacedOrder(Order $placedOrder): static
    {
        if (!$this->PlacedOrder->contains($placedOrder)) {
            $this->PlacedOrder->add($placedOrder);
            $placedOrder->setUser($this);
        }

        return $this;
    }

    public function removePlacedOrder(Order $placedOrder): static
    {
        if ($this->PlacedOrder->removeElement($placedOrder)) {
            // set the owning side to null (unless already changed)
            if ($placedOrder->getUser() === $this) {
                $placedOrder->setUser(null);
            }
        }

        return $this;
    }

	public function __toString(): string {
		return "User";
	}


}
