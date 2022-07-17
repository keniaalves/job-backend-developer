<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

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
        Product::find($id)
            ->update([
                'name' => $request->name,
                'price' => $request->price,
                'description' => $request->description,
                'category' => $request->category,
                'image_url' => $request->image_url,
            ]);
        $product = Product::find($id);

        return response()->json($product->toArray(), 200);
    }

    public function deleteProduct($id)
    {
        Product::find($id)->forceDelete();

        return response("Product  {$id} removed with success.", 200);
    }
}
