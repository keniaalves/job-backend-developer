<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'products:import {--id=}';
    protected $baseUrl =  'https://fakestoreapi.com/products/';

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
        $response = Http::get($this->baseUrl .'/'. $this->option('id'));
        $data = $response->json();
        Product::create([
            'name' => $data['title'],
            'price' => $data['price'],
            'description' => $data['description'],
            'category' => $data['category'],
            'image_url' => $data['image'],
        ]);
        $this->info('Success! The command has imported the product '. $this->option('id'));
    }
}
