<?php

namespace Tests\Feature\Models;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /**
     * A basic feature test to check if an authenticated user can get a collection of paginated products.
     *
     * @return void
     */
    public function test_can_return_collection_of_paginated_products()
    {
        $user = $this->createUser();
        Passport::actingAs($user);

        $response = $this->json('GET', '/api/v1/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'user_id', 'name', 'description', 'quantity', 'unit_price', 'amount_sold', 'created_at', 'updated_at']
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'from', 'to', 'path', 'per_page', 'total']
            ]);
    }

    /**
     * A basic feature test to check if an authenticated user can create a product.
     *
     * @return void
     */
    public function test_can_create_a_product()
    {
        $user = $this->createUser();
        Passport::actingAs($user);

        $response = $this->json('POST', '/api/v1/products', [
            'user_id' => $user->id,
            'name' => $name = 'Product-' . rand(1, 2000),
            'description' => $description = $this->faker()->sentence(),
            'quantity' => $quantity = rand(3, 99),
            'unit_price' => $unit_price = rand(34, 55),
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['id', 'user_id', 'name', 'description', 'quantity', 'unit_price', 'amount_sold', 'created_at', 'updated_at'])
            ->assertJson([
                'user_id' => $user->id,
                'name' => $name,
                'description' => $description,
                'quantity' => $quantity,
                'unit_price' => $unit_price,
            ]);
        $this->assertDatabaseHas('products', [
            'user_id' => $user->id,
            'name' => $name,
            'description' => $description,
            'quantity' => $quantity,
            'unit_price' => $unit_price,
        ]);
    }

    /**
     * A basic feature test to check if an authenticated user will get 404 found if product to fetch doesn't exist.
     *
     * @return void
     */
    public function test_will_fail_with_a_404_if_fetched_product_is_not_found()
    {
        $user = $this->createUser();
        Passport::actingAs($user);

        $response = $this->json('GET', '/api/v1/products/-1');
        $response->assertStatus(404);
    }

    /**
     * A basic feature test to check if an authenticated user can get back a product.
     *
     * @return void
     */
    public function test_can_return_a_product()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $product = $this->createProduct([
            'user_id' => $user->id,
            'name' => $name = 'Product-' . rand(1, 2000),
            'description' => $description = $this->faker()->sentence(),
            'quantity' => $quantity = rand(3, 99),
            'unit_price' => $unit_price = rand(34, 55),
        ]);

        $response = $this->json('GET', '/api/v1/products/' . $product->id);
        $response->assertStatus(200)
            ->assertJsonStructure(['id', 'user_id', 'name', 'description', 'quantity', 'unit_price', 'amount_sold', 'created_at', 'updated_at'])
            ->assertJson([
                'id' => $product->id,
                'user_id' => $user->id,
                'name' => $product->name,
                'description' => $product->description,
                'created_at' => (string) $product->created_at,
                'updated_at' => (string) $product->updated_at,
            ]);
    }

    /**
     * A basic feature test to check if an authenticated user will get 404 found if product to update doesn't exist.
     *
     * @return void
     */
    public function test_will_fail_with_a_404_if_updated_product_is_not_found()
    {
        $user = $this->createUser();
        Passport::actingAs($user);

        $response = $this->json('PUT', '/api/v1/products/-1');
        $response->assertStatus(404);
    }

    /**
     * A basic feature test to check if an authenticated user can update a product.
     *
     * @return void
     */
    public function test_can_update_a_product()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $product = $this->createProduct([
            'user_id' => $user->id,
            'name' => $name = 'Product-' . rand(1, 2000),
            'description' => $description = $this->faker()->sentence(),
            'quantity' => $quantity = rand(3, 99),
            'unit_price' => $unit_price = rand(34, 55),
        ]);

        $updated_name = $product->name . "_updated";
        $updated_unit_price = 38;

        $response = $this->json('PUT', '/api/v1/products/' . $product->id, [
            'name' => $updated_name,
            'description' => $product->description,
            'quantity' => $product->quantity,
            'unit_price' => $updated_unit_price,
        ]);
        $response->assertStatus(200)
            ->assertJson([
                'id' => $product->id,
                'user_id' => $user->id,
                'name' => $updated_name,
                'description' => $product->description,
                'quantity' => $product->quantity,
                'unit_price' => $updated_unit_price,
                'created_at' => (string) $product->created_at,
                'updated_at' => (string) $product->updated_at,
            ]);

        $this->assertDatabaseHas('products', [
            'name' => $updated_name,
            'unit_price' => $updated_unit_price,
        ]);
    }

    /**
     * A basic feature test to check if an authenticated user will get 404 found if product to delete doesn't exist.
     *
     * @return void
     */
    public function test_will_fail_with_a_404_if_product_to_delete_is_not_found()
    {
        $user = $this->createUser();
        Passport::actingAs($user);

        $response = $this->json('DELETE', '/api/v1/product/-1');
        $response->assertStatus(404);
    }

    /**
     * A basic feature test to check if an authenticated user can update a product.
     *
     * @return void
     */
    public function test_can_delete_a_product()
    {
        $user = $this->createUser();
        Passport::actingAs($user);
        $product = $this->createProduct([
            'user_id' => $user->id,
            'name' => $name = 'Product-' . rand(1, 2000),
            'description' => $description = $this->faker()->sentence(),
            'quantity' => $quantity = rand(3, 99),
            'unit_price' => $unit_price = rand(34, 55),
        ]);

        $response = $this->json('DELETE', '/api/v1/products/' . $product->id);
        $response->assertStatus(204)
            ->assertSee(null);

        $this->assertDatabaseMissing('products', [
            'name' => $product->name,
            'description' => $product->description,
            'quantity' => $product->quantity,
            'unit_price' => $product->unit_price,
            'amount_sold' => $product->amount_sold,
        ]);
    }
}
