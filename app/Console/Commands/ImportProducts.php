<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {--id=}';
    const BASEURL =  'https://fakestoreapi.com/products/';
    const DEFAULT_LIMIT = 30;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import products in a fake store external API';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            if ($this->option('id')) {
                $storedProduct = Product::where('external_id', (int)$this->option('id'))->first();
                if ($storedProduct) {
                    $this->info('Produto já existente na base de dados.');
                    $allowUpdate = $this->confirm('Deseja sincronizar as informações?');
                    if ($allowUpdate) {
                        $response = Http::get(self::BASEURL .'/'. $this->option('id'));
                        $product = $response->json();
                        if (!$product) {
                            return $this->line('Produto não encontrado!');
                        }
                        $storedProduct->update([
                            'name' => $product['title'],
                            'price' => $product['price'],
                            'description' => $product['description'],
                            'category' => $product['category'],
                            'image_url' => $product['image'],
                            'external_id' => $product['id']
                        ]);
                        return $this->info('Tudo certo! O produto '. $this->option('id') . ' foi atualizado com sucesso.');
                    }
                    return $this->line('Nenhuma ação foi realizada!');
                }
                $response = Http::get(self::BASEURL .'/'. $this->option('id'));
                $product = $response->json();
                Product::create([
                    'name' => $product['title'],
                    'price' => $product['price'],
                    'description' => $product['description'],
                    'category' => $product['category'],
                    'image_url' => $product['image'],
                    'external_id' => $product['id']
                ]);
                return $this->info('Tudo certo! O produto '. $this->option('id') . ' foi importado com sucesso.');
            }

            return $this->importSeveralProducts();
            
        } catch (\Exception $ex) {
            return $this->error('Erro! Ocorreu um erro inesperado. Mensagem de erro: '. $ex->getMessage());
        }
    }

    private function importSeveralProducts()
    {
        $limit = $this->ask('Informe o limite de produtos a serem importados (padrão 30)');
        $limit = $limit ?? self::DEFAULT_LIMIT;
        try {
            $bar = $this->output->createProgressBar($limit);
            $response = Http::get(self::BASEURL);
            $products = $response->json();
            $allowUpdate = $this->confirm('Deseja sincronizar as informações?');
            foreach ($products as $key => $product) {
                $bar->start();
                DB::beginTransaction();
                if ($allowUpdate) {
                    Product::UpdateOrCreate([
                        'external_id' => $product['id'],
                        'name' => $product['title'],
                        'price' => $product['price'],
                        'description' => $product['description'],
                        'category' => $product['category'],
                        'image_url' => $product['image'],
                        'external_id' => $product['id']
                    ]);
                } else {
                    Product::firstOrCreate([
                        'external_id' => $product['id'],
                        'name' => $product['title'],
                        'price' => $product['price'],
                        'description' => $product['description'],
                        'category' => $product['category'],
                        'image_url' => $product['image'],
                        'external_id' => $product['id']
                    ]);
                }
                $bar->advance();
            }
            DB::commit();
            $bar->finish();
            return $this->info('Tudo certo! Os produtos foram importados com sucesso.');
        } catch (\Exception $ex) {
            DB::rollBack();
            $bar->finish();
            return $this->error('Erro! Ocorreu um erro inesperado. Mensagem de erro: '. $ex->getMessage());
        }
    }
}
