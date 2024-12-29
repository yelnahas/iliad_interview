<?php

namespace App\Repositories;

use App\Models\Product;
use App\Contracts\Repositories\StockRepositoryInterface;
use Exception;

class StockRepository implements StockRepositoryInterface
{
    public function decrement(int $productId, int $quantity): void
    {
        $product = Product::findOrFail($productId);

        if ($product->stock < $quantity) {
            throw new Exception("Insufficient stock for product ID {$productId}");
        }

        $product->decrement('stock', $quantity);
    }

    public function increment(int $productId, int $quantity): void
    {
        $product = Product::findOrFail($productId);

        $product->increment('stock', $quantity);
    }

    public function checkAvailability(int $productId, int $quantity): bool
    {
        $product = Product::findOrFail($productId);

        return $product->stock >= $quantity;
    }
}

?>