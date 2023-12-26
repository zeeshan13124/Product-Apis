<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetProductsApiTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_can_get_all_products()
    {
        // Create a user in the database
        $user = User::factory()->create();
        // Authenticate as the user
        $this->actingAs($user, 'api');

        // Make a request to the API endpoint
        $response = $this->get('/api/products/getProducts');

        // Assert the response status is 200 (OK)
        $response->assertStatus(200);

        // Assert the response structure
        $response->assertJsonStructure([
            '*' => ['name', 'price', 'category'],
        ]);

    }
}
