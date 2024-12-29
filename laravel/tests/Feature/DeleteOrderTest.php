<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DeleteOrderTest extends TestCase
{

    use RefreshDatabase;

    /**
     * Test delete an order with a non-existent ID.
     */
    public function test_delete_order_with_non_existent_id()
    {
        // Get Token
        $token = config('jwt.secret');
        
        // Use an ID that does not exist in the database
        $nonExistentId = 9999; // Assume this ID does not exist

        // Perform the delete request with the non-existent ID
        $response = $this->json('DELETE', "/api/orders/{$nonExistentId}", [], [
            'Authorization' => 'Bearer ' . $token, 
        ]);

        // Verify that the response is correct and the proper error message is returned
        $response->assertStatus(404)
                ->assertJson([
                    'status' => 'error',
                    'message' => 'Order not found.',
                ]);
    }

    /**
     * Test delete an order.
     */
    public function test_delete_order()
    {
        // Get Token
        $token = config('jwt.secret');
        
        // Create an order for the test
        $order = \App\Models\Order::factory()->create([
            'name' => 'Test Order',
            'description' => 'Order to be deleted'
        ]);

        // Perform the delete request to delete the order
        $response = $this->json('DELETE', "/api/orders/{$order->id}", [], [
            'Authorization' => 'Bearer ' . $token, 
        ]);

        // Verify that the response is correct
        $response->assertStatus(200)
                ->assertJson([
                    'status' => 'success',
                    'message' => 'Order deleted successfully.',
                ]);

        // Verify that the order has been actually deleted from the database
        $this->assertDatabaseMissing('orders', [
            'id' => $order->id,
        ]);
    }


    public function test_delete_order_without_token()
    {
        // Create an order for the test
        $order = \App\Models\Order::factory()->create([
            'name' => 'Test Order',
            'description' => 'Order to be deleted'
        ]);

        // Perform the delete request without a token
        $response = $this->json('DELETE', "/api/orders/{$order->id}");

        // Verify that the response status is 401
        $response->assertStatus(401)
                ->assertJson([
                    'error' => 'Unauthorized',
                ]);

        // Verify that the order still exists in the database
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
        ]);
    }


}

?>
