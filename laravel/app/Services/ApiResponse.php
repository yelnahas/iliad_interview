<?php

namespace App\Services;

use App\Contracts\ApiResponseInterface;

class ApiResponse implements ApiResponseInterface
{

    public function success($data = [], string $message = 'Operation completed successfully.', int $statusCode = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'errors' => null,
        ], $statusCode);
    }

    public function error(string $message = 'An error occurred.',  array $errors = [], int $statusCode = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'data' => null,
            'errors' => $errors,
        ], $statusCode);
    }
}

?>