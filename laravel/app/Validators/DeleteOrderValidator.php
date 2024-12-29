<?php

namespace App\Validators;

use App\Contracts\OrderValidator;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;


class DeleteOrderValidator implements OrderValidator
{

    public function validate($data): void
    {

        if (!is_string($data)) {
            throw new InvalidArgumentException('Expected data to be a string.');
        }

        $validator = Validator::make(['id' => $data], [
            'id' => 'required|string|exists:orders,id',
        ], [
            'exists' => 'Order not found.',  // Custom error message
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