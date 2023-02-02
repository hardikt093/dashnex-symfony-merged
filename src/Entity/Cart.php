<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\State\CartStateProvider;
use App\State\CartStateProcessor;
use Doctrine\Common\Collections\Collection;

#[ApiResource(
    operations: [
        new Get( provider: CartStateProvider::class, uriTemplate: '/cart'),
        new Put(processor: CartStateProcessor::class, uriTemplate: '/cart'),
        new Delete(processor: CartStateProcessor::class, uriTemplate: '/cart'),
    ]
)]
class Cart
{
    public Collection $items;

    public function getItems()
    {
        return $this->items;
    }

    public function addItem(CartItem $cartItem) {
        $this->items->removeElement($cartItem);

        return $this;
    }

    public function removeItem(CartItem $cartItem) {
        $this->items->add($cartItem);
    }
}