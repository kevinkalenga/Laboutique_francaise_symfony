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
    private ?\DateTime $createdAt = null;

    #[ORM\Column]
    private ?int $state = null;

    #[ORM\Column(length: 255)]
    private ?string $carrierName = null;

    #[ORM\Column]
    private ?float $carrierPrice = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $delivery = null;

    /**
     * @var Collection<int, OrderDetail>
     */
    #[ORM\OneToMany(targetEntity: OrderDetail::class, mappedBy: 'myOrder', cascade: ['persist'])]
    private Collection $orderDetails;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $stripe_session_id = null;

    public function __construct()
    {
        $this->orderDetails = new ArrayCollection();
    }

     // 
    public function getTotalWt()
    {
        $totalTTC = 0;
        $products = $this->getOrderDetails();

        foreach ($products as $product) {
            // dd($product);
            $coeff = 1 + ($product->getProductTva() / 100);
            $totalTTC += ($product->getProductPrice() * $coeff) * $product->getProductQuantity();
        }


        return $totalTTC + $this->getCarrierPrice();
    }


    public function getTotalTva()
    {
        $totalTva = 0;
        $products = $this->getOrderDetails();

        foreach ($products as $product) {
            // dd($product);
            $coeff = $product->getProductTva() / 100;
            $totalTva += $product->getProductPrice() * $coeff;
        }
        return $totalTva;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTime $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getState(): ?int
    {
        return $this->state;
    }

    public function setState(int $state): static
    {
         // Si la commande est annulée (5), on refuse tout changement d’état
          if ($this->state === 5 && $newState !== 5) {
             throw new \LogicException('Une commande annulée ne peut pas changer d\'état.');
          }
        $this->state = $state;

        return $this;
    }

    public function getCarrierName(): ?string
    {
        return $this->carrierName;
    }

    public function setCarrierName(string $carrierName): static
    {
        $this->carrierName = $carrierName;

        return $this;
    }

    public function getCarrierPrice(): ?float
    {
        return $this->carrierPrice;
    }

    public function setCarrierPrice(float $carrierPrice): static
    {
        $this->carrierPrice = $carrierPrice;

        return $this;
    }

    public function getDelivery(): ?string
    {
        return $this->delivery;
    }

    public function setDelivery(string $delivery): static
    {
        $this->delivery = $delivery;

        return $this;
    }

    /**
     * @return Collection<int, OrderDetail>
     */
    public function getOrderDetails(): Collection
    {
        return $this->orderDetails;
    }

    public function addOrderDetail(OrderDetail $orderDetail): static
    {
        if (!$this->orderDetails->contains($orderDetail)) {
            $this->orderDetails->add($orderDetail);
            $orderDetail->setMyOrder($this);
        }

        return $this;
    }

    public function removeOrderDetail(OrderDetail $orderDetail): static
    {
        if ($this->orderDetails->removeElement($orderDetail)) {
            // set the owning side to null (unless already changed)
            if ($orderDetail->getMyOrder() === $this) {
                $orderDetail->setMyOrder(null);
            }
        }

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

    public function getStripeSessionId(): ?string
    {
        return $this->stripe_session_id;
    }

    public function setStripeSessionId(?string $stripe_session_id): static
    {
        $this->stripe_session_id = $stripe_session_id;

        return $this;
    }

    public function getStateLabel(): string
{
    return match ($this->state) {
        1 => '🕓 En attente de paiement',
        2 => '✅ Payée',
        3 => '📦 Préparation',
        4 => '🚚 Expédiée',
        5 => '❌ Annulée',
        default => '❓ Inconnu',
    };
}

}
