<?php

namespace App\Contracts;

interface OrderInterface
{
    public function searchOrder(array $data);
    public function createOrder(array $data);
    public function updateOrder(array $data);
    public function deleteOrder(string $data);
}

?>