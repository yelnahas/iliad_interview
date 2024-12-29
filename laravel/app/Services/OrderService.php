<?php

namespace App\Services;

use App\Contracts\OrderInterface;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use App\Validators\OrderValidatorFactory;
use App\Contracts\Repositories\OrderRepositoryInterface;
use App\Contracts\Repositories\StockRepositoryInterface;
use Exception;

class OrderService implements OrderInterface
{
    protected $validators;

    protected $orderRepository;
    protected $stockRepository;

    /**
     * Constructor OrderService
     *
     * @param OrderValidatorFactory $validatorFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param StockRepositoryInterface $stockRepository
     * 
     */
    public function __construct(OrderValidatorFactory $validatorFactory, OrderRepositoryInterface $orderRepository, StockRepositoryInterface $stockRepository)
    {
        $this->validators = $validatorFactory->create();

        $this->orderRepository = $orderRepository;
        $this->stockRepository = $stockRepository;

    }

    /**
     * Search for an order based on given criteria using Algolia.
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function searchOrder(array $data): array
    {
        // Validates search orders
        $this->validators['search']->validate($data);

        $orders = $this->orderRepository->search($data);

        if (count($orders) === 0) {
            throw new Exception("Order not found.", 404);
        }

        return $orders;
    }

    /**
     * Create a new order and associate products with it.
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function createOrder(array $data): Order
    {
        // Validates order creation data
        $this->validators['create']->validate($data);

        return DB::transaction(function () use ($data) {
            $order = $this->orderRepository->create($data);

            foreach ($data['products'] as $productData) {
                $this->stockRepository->decrement($productData['id'], $productData['quantity']);
                $order->products()->attach($productData['id'], ['quantity' => $productData['quantity']]);
            }

            return $order;
        });
    }

    /**
     * Update an existing order and adjust the product stock accordingly.
     *
     * @param array $data
     * @return Order
     * @throws Exception
     */
    public function updateOrder(array $data): Order
    {
        // Validates order update data
        $this->validators['update']->validate($data);

        return DB::transaction(function () use ($data) {
            $order = $this->orderRepository->findById($data['id']);

            foreach ($order->products as $product) {
                $this->stockRepository->increment($product->id, $product->pivot->quantity);
            }

            $order->products()->detach();
            $this->orderRepository->update($order, $data);

            foreach ($data['products'] as $productData) {
                $this->stockRepository->decrement($productData['id'], $productData['quantity']);
                $order->products()->attach($productData['id'], ['quantity' => $productData['quantity']]);
            }

            return $order;
        });
    }

    /**
     * Delete an order and restore product stock.
     *
     * @param string $data
     * @return void
     * @throws Exception
     */
    public function deleteOrder(string $data) : void
    {
   
        // Validates order deletion
        $this->validators['delete']->validate($data);

        DB::transaction(function () use ($data) {
            $order = $this->orderRepository->findById($data);

            foreach ($order->products as $product) {
                $this->stockRepository->increment($product->id, $product->pivot->quantity);
            }

            $order->products()->detach();
            $this->orderRepository->delete($order);
        });
        
    }
}

?>