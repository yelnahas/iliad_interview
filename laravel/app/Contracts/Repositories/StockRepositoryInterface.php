<?php

namespace App\Contracts\Repositories;

interface StockRepositoryInterface
{
    public function decrement(int $productId, int $quantity): void;
    public function increment(int $productId, int $quantity): void;
    public function checkAvailability(int $productId, int $quantity): bool;
}

?>