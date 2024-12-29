<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test updating the order with valid data.
     *
     * @return void
     */
    public function test_update_order_with_valid_data()
    {
        // Get Token
        $token = config('jwt.secret');
        
        // Create a product
        $product = \App\Models\Product::factory()->create();

        // Create an order
        $order = \App\Models\Order::factory()->create([
            'date' => date('Y-m-d'),
            'name' => 'Test Order',
            'description' => 'questo'
        ]);

        $order->products()->attach($product->id, ['quantity' => $product->stock]);

        // Valid data for updating the order
        $payload = [
            'id' => $order->id,
            'name' => 'Order updated',
            'description' => 'This is an update',
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 5,
                ]
            ]
        ];

        // Perform the PUT request to update the order
        $response = $this->json('PUT', '/api/orders', $payload, [
            'Authorization' => 'Bearer ' . $token,  // Ensure you have the bearer token
        ]);

        // Verify that the response is correct
        $response->assertStatus(200)
         ->assertJson([
             'status' => 'success',
             'message' => 'Order updated successfully.',
         ])
         ->assertJsonStructure([
             'status',
             'message',
             'data' => [
                 'id',
                 'name',
                 'description',
                 'date',
                 'created_at',
                 'updated_at',
                 'products' => [
                     '*' => [  
                         'id',
                         'name',
                         'price',
                         'stock',
                         'created_at',
                         'updated_at',
                         'pivot' => [
                             'order_id',
                             'product_id',
                             'quantity',
                             'created_at',
                             'updated_at'
                         ]
                     ]
                 ]
             ],
             'errors'
         ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'name' => 'Order updated',
            'description' => 'This is an update',
        ]);
    }

    /**
     * Test that verifies an error when the input data is not valid.
     *
     * @return void
     */
    public function test_update_order_with_invalid_data()
    {

        $token = config('jwt.secret');

        $product = \App\Models\Product::factory()->create();
        $order = \App\Models\Order::factory()->create();

        // Invalid input data (for example, missing order name)
        $payload = [
            'id' => $order->id,
            'name' => '',  // Empty name field, which should cause an error
            'description' => 'This is an update',
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => $product->stock,
                ]
            ]
        ];

        // Perform the PUT request
        $response = $this->json('PUT', '/api/orders', $payload, [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Verify that the response contains an error
        $response->assertStatus(400)
                 ->assertJson([
                     'status' => 'error',
                     'message' => 'The name field is required.',
                 ]);
    }


    /**
     * Test updating an order without authentication.
     */
    public function test_update_order_without_authentication()
    {

        $product = \App\Models\Product::factory()->create();
        $order = \App\Models\Order::factory()->create();

        // Prepare the payload
        $payload = [
            'id' => $order->id,
            'name' => 'Order updated',
            'description' => 'This is an update',
            'products' => [
                [
                    'id' => $product->id,
                    'quantity' => 5,
                ]
            ]
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

?>
