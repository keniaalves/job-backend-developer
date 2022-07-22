<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ApiController extends Controller
{
    public function getAllProducts(Request $request)
    {
        if ($request->image === false) {
            $filteredProducts = Product::all()->makeHidden(['image_url']);

            return response()->json($filteredProducts->all());
        }
        return response()->json(Product::all());
    }

    public function getProduct(Request $request, $id = null)
    {
        $product = Product::find($id);

        if (!$product && $request->category && $request->name) {
            $product = Product::where('category', $request->category)
                ->where('name', $request->name)
                ->first();
        }

        return response()->json($product->toArray(), 200);
    }

    public function getProductsByCategory($category)
    {
        $products = Product::where('category', $category)->get();

        return response()->json($products);
    }

    public function createProduct(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'required|max:255',
            'price' => 'required|numeric|gt:0',
            'description' => 'required',
            'category' => 'required|max:255',
            'image_url' => 'nullable'

        ],[
            '*.required' => 'Campo obrigatório',
            '*.max' => 'O campo não pode ultrapassar de 255 caracteres',
            'price.*' => 'Campo obrigatório. O preço deve ser um número maior que zero'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
            'image_url' => $request->image_url,
        ]);
        
        return response()->json($product->toArray(), 200);
    }

    public function updateProduct(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name'=> 'filled|max:255',
            'price' => 'filled|numeric|gt:0',
            'description' => 'filled',
            'category' => 'filled|max:255',
            'image_url' => 'nullable'

        ],[
            '*.filled' => 'Esse campo não pode ser vazio',
            '*.max' => 'O campo não pode ultrapassar de 255 caracteres',
            'price.*' => 'O preço deve ser um número maior que zero'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $product = Product::find($id);
        if ($product) {
            $product->update([
                'name' => $request->name ?? $product->name,
                'price' => $request->price ?? $product->price,
                'description' => $request->description ?? $product->description,
                'category' => $request->category ?? $product->category,
                'image_url' => $request->image_url ?? $product->image_url,
            ]);
            
        }

        return response()->json($product->toArray(), 200);
    }

    public function deleteProduct($id)
    {
        Product::find($id)->forceDelete();

        return response("Product  {$id} removed with success.", 200);
    }
}
