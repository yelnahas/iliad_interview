<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test creating a new order with valid data.
     */
    public function test_create_order_with_valid_data()
    {
        // Get Token
        $token = config('jwt.secret');

        // Create some products
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        //dd($product1, $product2);

        // Prepare the payload
        $payload = [
            'name' => 'Test Order',
            'description' => 'This is a test order',
            'products' => [
                ['id' => $product1->id, 'quantity' => 5],
                ['id' => $product2->id, 'quantity' => 2],
            ],
        ];

        // Make the POST request
        $response = $this->json('POST', '/api/orders', $payload, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        //dd($response);

        // Assert the response
        $response->assertStatus(201)
            ->assertJson([
                'status' => "success",
                'message' => 'Order created successfully.',
            ])
            ->assertJsonStructure([
                'status',
                'data' => [
                    'id',
                    'name',
                    'description',
                    'created_at',
                    'updated_at',
                ],
                'message',
                'errors'
            ]);
    }

    /**
     * Test creating a new order with invalid data.
     */
    public function test_create_order_with_invalid_data()
    {
        // Get Token
        $token = config('jwt.secret');

        // Prepare the invalid payload (missing required fields)
        $payload = [
            'description' => 'This is a test order',
            'products' => [
                ['id' => 1, 'quantity' => 5],
                ['id' => 2, 'quantity' => 2],
            ],
        ];

        // Make the POST request
        $response = $this->json('POST', '/api/orders', $payload, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert the response
        $response->assertStatus(400)
            ->assertJson([
                'status' => 'error',
            ])
            ->assertSee('The name field is required.')
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'errors'
            ]);
    }

    /**
     * Test creating a new order without authentication.
     */
    public function test_create_order_without_authentication()
    {
        // Prepare the payload
        $payload = [
            'name' => 'Test Order',
            'description' => 'This is a test order',
            'products' => [
                ['id' => 1, 'quantity' => 5],
                ['id' => 2, 'quantity' => 2],
            ],
        ];

        // Make the POST request without a token
        $response = $this->json('POST', '/api/orders', $payload);

        // Assert the response
        $response->assertStatus(401)
            ->assertJson([
                'error' => 'Unauthorized',
            ]);
    }
}
