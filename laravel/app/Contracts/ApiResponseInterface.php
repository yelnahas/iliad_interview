<?php

namespace App\Contracts;

interface ApiResponseInterface
{
    public function success($data = [], string $message = "Operation completed successfully.", int $statusCode = 200);

    public function error(string $message = 'An error occurred.', array $errors = [], int $statusCode = 400);

}