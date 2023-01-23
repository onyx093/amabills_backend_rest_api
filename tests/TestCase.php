<?php

namespace Tests;

use App\Models\User;
use App\Models\Product;
use App\Http\Resources\ProductResource;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    public function createUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        return $user;
    }

    public function createProduct(array $attributes = []): ProductResource
    {
        $post = Product::factory()->create($attributes);
        return new ProductResource($post);
    }
}
