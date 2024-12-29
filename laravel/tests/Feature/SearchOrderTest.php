<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

class SearchOrderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test search orders with valid parameters and Bearer token.
     *
     * @return void
     */
    public function test_search_orders_with_valid_parameters_and_bearer_token()
    {
        // Get Token
        $token = config('jwt.secret');
        
        // Create a product
        $product = \App\Models\Product::factory()->create([
            'name' => 'First product',
            'price' => 100.2,
            'stock' => 30
        ]);

        // Create an order
        $order = \App\Models\Order::factory()->create([
            'date' => date('Y-m-d'),
            'name' => 'Test Order',
            'description' => 'questo'
        ]);

        $order->products()->attach($product->id, ['quantity' => $product->stock]);

        // Using sleep ensures Algolia has time to sync the data before running search test
        sleep(3);

        // Send request to the API endpoint with the Bearer token
        $response = $this->json('GET', '/api/orders/search', [
            'date' => $order->date, // required field
            'name' => $order->name, // optional field
            'description' => $order->description, // optional field
            'products' => [
                'id' => $product->id,
                'quantity' => $product->stock
            ]
        ], [
            'Authorization' => 'Bearer ' . $token, 
        ]);

        // Assert the response status and content
        $response->assertStatus(200)
            ->assertJson(function (AssertableJson $json) use ($order, $product) {
                $json->where('status', 'success')
                    ->where('message', 'Order viewed successfully.')
                    ->has('data')
                    ->has('errors');
            });
    }

    /**
     * Test search orders with missing required 'date' parameter and Bearer token.
     *
     * @return void
     */
    public function test_search_orders_missing_required_date_and_bearer_token()
    {
        // Get token
        $token = config('jwt.secret');

        // Send request without the required 'date' parameter
        $response = $this->json('GET', '/api/orders/search', [
            'name' => 'Test Order',
            'description' => 'questo',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert the response is a 400 error with correct message
        $response->assertStatus(400)
                ->assertJson(function (AssertableJson $json) {
                    $json->where('status', 'error')
                        ->where('message', "The date field is required.")
                        ->has('data')
                        ->has('errors');
                });
    }

    /**
     * Test search orders with invalid date format and Bearer token.
     *
     * @return void
     */
    public function test_search_orders_with_invalid_date_format_and_bearer_token()
    {
        // Get token
        $token = config('jwt.secret');

        // Send request with invalid date format
        $response = $this->json('GET', '/api/orders/search', [
            'date' => '2024-12-32',
            'name' => 'Test Order',
            'description' => 'questo',
        ], [
            'Authorization' => 'Bearer ' . $token,
        ]);

        // Assert the response is a 400 error
        $response->assertStatus(400)
                ->assertJson(function (AssertableJson $json) {
                    $json->where('status', 'error')
                        ->where('message', "The date field must be a valid date.")
                        ->has('data')
                        ->has('errors');
                });
    }
}
