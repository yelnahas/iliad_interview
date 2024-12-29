<?php

namespace App\Contracts\Repositories;

use App\Models\Order;

interface OrderRepositoryInterface
{
    public function findById(int $id): ?Order;
    public function create(array $data): Order;
    public function update(Order $order, array $data): Order;
    public function delete(Order $order): void;
    public function search(array $filters): array;
}

?>