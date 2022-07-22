<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Resources\Product as ProductResource;

class ApiController extends Controller
{
    public function getAllProducts(Request $request)
    {
        if ($request->image === false) {
            $filteredProducts = Product::whereNull('image_url');

            return response()->json(ProductResource::collection($filteredProducts->get()));
        } else if ($request->image === true) {
            $filteredProducts = Product::whereNotNull('image_url');

            return response()->json(ProductResource::collection($filteredProducts->get()));
        }

        return response()->json(ProductResource::collection(Product::all()));
    }

    public function getProduct(Request $request, $id = null)
    {
        $product = Product::find($id);

        if (!$product && $request->category && $request->name) {
            $product = Product::where('category', $request->category)
                ->where('name', $request->name)
                ->first();
        }

        if (!$product) {
            return response()->json("Produto não encontrado.");
        }

        return response()->json(new ProductResource($product), 200);
    }

    public function getProductsByCategory($category)
    {
        $products = Product::where('category', $category)->get();

        return response()->json($products);
    }

    public function createProduct(ProductRequest $request)
    {
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'category' => $request->category,
            'image_url' => $request->image,
        ]);

        return response()->json("Produto criado com sucesso! ID: {$product->id}", 200);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->update([
                'name' => $request->name ?? $product->name,
                'price' => $request->price ?? $product->price,
                'description' => $request->description ?? $product->description,
                'category' => $request->category ?? $product->category,
                'image_url' => $request->image ?? $product->image_url,
            ]);

            return response()->json("Produto atualizado com sucesso! ID: {$id}", 200);
        }

        return response()->json("Produto {$id} não encontrado.", 404);
    }

    public function deleteProduct($id)
    {
        $product = Product::find($id);
        if ($product) {
            $product->forceDelete();

            return response()->json("Produto removido com sucesso! ID: {$id}", 200);
        }

        return response()->json("Produto {$id} não encontrado.", 404);
    }
}
