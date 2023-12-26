<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SaveProductApiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_create_product()
    {
        // Create a user in the database
        $user = User::factory()->create();

        // Authenticate as the user
        $this->actingAs($user, 'api');

        // Make a request to the API endpoint to create a product
        $productData = [
            'name' => 'Test Product',
            'price' => 29.99,
            'category' => 'Electronics',
        ];
        $response = $this->postJson('/api/products/saveProducts', $productData);
        // Assert the response status is 201 (Created)
        $response->assertStatus(201);
        // Check that the product was actually created in the database
        $this->assertDatabaseHas('products', $productData);
    }
}
