<?php

namespace App\Validators;

class OrderValidatorFactory
{
    public function create() : array
    {
        return [
            'search' => new SearchOrderValidator(),
            'create' => new CreateOrderValidator(),
            'update' => new UpdateOrderValidator(),
            'delete' => new DeleteOrderValidator(),
        ];
    }
}

?>
