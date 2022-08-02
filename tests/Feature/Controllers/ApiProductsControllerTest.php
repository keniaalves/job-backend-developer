<?php

namespace Tests\Feature\Controllers;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ApiProductsControllerTest extends TestCase
{
    use DatabaseMigrations;

    public function test_getAllProducts_returns_all_products()
    {
        Product::factory()->count(3)->create();
        $product = Product::first();
        $product->name = 'teste';
        $product->save();

        $response = $this->get('/api/products');

        $response->assertJsonFragment([
            'name' => 'teste'
        ]);
        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_getProduct_returns_a_product_by_id()
    {
        Product::factory()->count(3)->create();
        $product = Product::first();
        $product->name = 'teste';
        $product->save();

        $response = $this->get('/api/product/1');
        
        $response->assertJsonFragment([
            'name' => 'teste'
        ]);
        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    public function test_getProduct_returns_a_product_by_name_and_category()
    {
        Product::factory()->count(3)->create();
        $product = Product::first();
        $product->name = 'teste';
        $product->category = 'teste';
        $product->save();

        $response = $this->json('GET', '/api/product', [
            'name' => 'teste',
            'category' => 'teste'
        ]);
        
        $response->assertJsonFragment([
            'category' => 'teste'
        ]);
        $response->assertStatus(200);
        $response->assertJsonCount(5);
    }

    public function test_getProductsByCategory_returns_the_product_in_the_category()
    {
        Product::factory()->create(['category' => 'searched']);
        Product::factory()->create(['category' => 'notsearched']);

        $response = $this->get('/api/products/category/searched');
        
        $response->assertJsonFragment([
            'category' => 'searched'
        ]);
        $response->assertJsonMissing(['category', 'notsearched']);
        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_createProduct_saves_a_product_in_database()
    {
        $product = Product::factory()->make();
        $response = $this->json('POST', '/api/product', $product->toArray());

        $response->assertStatus(200);
        $response->assertSee('Produto criado com sucesso!');

        $this->assertDatabaseCount('products', 1);
    }

    public function test_createProduct_doesnt_save_product_in_database_with_the_same_name()
    {
        Product::factory()->create(['name' => 'Nome Usado']);
        $product = Product::factory()->make(['name' => 'Nome Usado']);
        $response = $this->json('POST', '/api/product', $product->toArray());

        $response->assertStatus(422);
        $this->assertDatabaseCount('products', 1);
    }

    public function test_updateProduct_update_a_product_in_database()
    {
        Product::factory()->create(['name' => 'Nome Antigo']);

        $response = $this->json('PUT', '/api/product/1', ['name' => 'Novo Nome']);
        $response->assertStatus(200);
        $response->assertSee('Produto atualizado com sucesso!');

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', ['name' => 'Novo Nome']);
    }

    public function test_deleteProduct_erases_a_product_in_database()
    {
        Product::factory()->create();

        $response = $this->json('DELETE', '/api/product/1');
        $response->assertStatus(200);
        $response->assertSee('Produto removido com sucesso!');

        $this->assertDatabaseCount('products', 0);
    }
}
