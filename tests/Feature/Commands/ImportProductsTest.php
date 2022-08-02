<?php

namespace Tests\Feature\Commands;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ImportProductsTest extends TestCase
{
    use DatabaseMigrations;

    public function test_import_single_product_from_external_api_saves_a_product_in_database()
    {
        Http::fake([
            '*' => Http::response([
                'id' => 1,
                "title" => "product name",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ], 200)
        ]);
        $this->artisan('products:import', ['--id' => 1])->assertSuccessful();
        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', ['name' => 'product name', 'external_id' => 1]);
    }

    public function test_import_single_product_from_external_api_update_existing_product_when_confirms_sync()
    {
        Product::factory()->create(['name' => 'Nome Antigo', 'external_id' => 1]);
        Http::fake([
            '*' => Http::response([
                'id' => 1,
                "title" => "product name",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ], 200)
        ]);

        $this->artisan('products:import', ['--id' => 1])
            ->expectsConfirmation('Deseja sincronizar as informações?', 'yes')
            ->assertSuccessful();

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', ['name' => 'product name', 'external_id' => 1]);
    }

    public function test_import_single_existing_product_from_external_api_do_nothing_when_refuse_sync()
    {
        Product::factory()->create(['name' => 'Nome Antigo', 'external_id' => 1]);
        Http::fake([
            '*' => Http::response([
                'id' => 2,
                "title" => "product name 2",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ], 200)
        ]);

        $this->artisan('products:import', ['--id' => 1])
            ->expectsConfirmation('Deseja sincronizar as informações?', 'no')
            ->expectsOutput('Nenhuma ação foi realizada!')
            ->assertSuccessful();

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', ['name' => 'Nome Antigo', 'external_id' => 1]);
    }

    public function test_import_single_new_product_creates_product_in_database()
    {
        Http::fake([
            '*' => Http::response([
                'id' => 1,
                "title" => "product name",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ], 200)
        ]);

        $this->artisan('products:import', ['--id' => 1])->assertSuccessful();

        $this->assertDatabaseCount('products', 1);
        $this->assertDatabaseHas('products', ['name' => 'product name', 'external_id' => 1]);
    }

    public function test_import_several_products_if_has_no_option_id()
    {
        Http::fake([
            '*' => Http::response([[
                'id' => 1,
                "title" => "product name 1",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ],[
                'id' => 2,
                "title" => "product name 2",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ]], 200)
        ]);
        $this->artisan('products:import')
            ->expectsQuestion('Informe o limite de produtos a serem importados (padrão 30)', 2)
            ->expectsConfirmation('Deseja sincronizar as informações?', 'no')
            ->assertSuccessful();

        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseHas('products', ['name' => 'product name 1', 'external_id' => 1]);
        $this->assertDatabaseHas('products', ['name' => 'product name 2', 'external_id' => 2]);
    }

    public function test_import_several_products_if_has_no_option_id_and_doesnt_update_if_has_negative_answer()
    {
        Product::factory()->create(['name' => 'Nome Antigo', 'external_id' => 1]);
        Http::fake([
            '*' => Http::response([[
                'id' => 1,
                "title" => "product name 1",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ],[
                'id' => 2,
                "title" => "product name 2",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ]], 200)
        ]);
        $this->artisan('products:import')
            ->expectsQuestion('Informe o limite de produtos a serem importados (padrão 30)', 2)
            ->expectsConfirmation('Deseja sincronizar as informações?', 'no')
            ->assertSuccessful();

        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseHas('products', ['name' => 'Nome Antigo', 'external_id' => 1]);
        $this->assertDatabaseHas('products', ['name' => 'product name 2', 'external_id' => 2]);
    }

    public function test_import_several_products_if_has_no_option_id_and_update_if_has_positive_answer()
    {
        Product::factory()->create(['name' => 'Nome Antigo', 'external_id' => 1]);
        Http::fake([
            '*' => Http::response([[
                'id' => 1,
                "title" => "product name 1",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ],[
                'id' => 2,
                "title" => "product name 2",
                "price" =>  109.95,
                "category" => "test",
                "description" => "Neque porro quisquam est qui dolorem ipsum quia dolor sit amet",
                "image" => "https://fakestoreapi.com/img/81fPKd-2AYL._AC_SL1500_.jpg"
            ]], 200)
        ]);
        $this->artisan('products:import')
            ->expectsQuestion('Informe o limite de produtos a serem importados (padrão 30)', 2)
            ->expectsConfirmation('Deseja sincronizar as informações?', 'yes')
            ->assertSuccessful();

        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseHas('products', ['name' => 'product name 1', 'external_id' => 1]);
        $this->assertDatabaseHas('products', ['name' => 'product name 2', 'external_id' => 2]);
    }
}
