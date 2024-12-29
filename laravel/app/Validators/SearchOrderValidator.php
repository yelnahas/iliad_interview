<?php

namespace App\Validators;

use App\Contracts\OrderValidator;
use Illuminate\Support\Facades\Validator;
use InvalidArgumentException;

class SearchOrderValidator implements OrderValidator
{
    public function validate($data): void
    {

        if (!is_array($data)) {
            throw new InvalidArgumentException('Expected data to be an array.');
        }
        
        Validator::make($data, [
            'date' => 'required|date',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
        ])->validate();
    }
}

?>