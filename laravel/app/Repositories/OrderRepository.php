<?php

namespace App\Repositories;

use App\Models\Order;
use App\Contracts\Repositories\OrderRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;

class OrderRepository implements OrderRepositoryInterface
{
    public function findById(int $id): ?Order
    {
        return Order::find($id);
    }

    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function update(Order $order, array $data): Order
    {
        $order->update($data);
        return $order;
    }

    public function delete(Order $order): void
    {
        $order->delete();
    }

    public function search(array $filters): array
    {
        return Order::search($filters['name'] ?? $filters['description'] ?? '')
        ->query(function (Builder $query) use ($filters){
            return $query
                ->with('products') // Load related products
                ->when(!empty($filters['description']), function ($query) use ($filters) {
                    $query->where('description', 'like', '%' . $filters['description'] . '%');
                })
                ->where('date', $filters['date']);
        })
        ->get()
        ->toArray();

    }
}
