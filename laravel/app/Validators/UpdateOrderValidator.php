<?php

namespace App\Validators;

use App\Contracts\OrderValidator;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class UpdateOrderValidator implements OrderValidator
{
    public function validate($data): void
    {

        if (!is_array($data)) {
            throw new InvalidArgumentException('Expected data to be an array.');
        }
        
        $validator = Validator::make($data, [
            'id' => 'required|exists:orders,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.id' => 'required_with:products|exists:products,id',
            'products.*.quantity' => 'required_with:products|numeric|min:1',
        ], [
            'exists' => 'Order not found.',  // Custom error message
            'min' => 'The value must be at least :min.'
        ]);

        if ($validator->fails()) {

            $statusCode = match ($validator->errors()->first()) {
                'Order not found.' => 404,
                default => '400',
            };
            
            throw new \Exception($validator->errors()->first(), $statusCode);

        }

    }
}


?>