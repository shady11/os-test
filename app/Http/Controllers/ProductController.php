<?php

namespace App\Http\Controllers;

use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query();

        if($request->has('name')) {
            $products = Product::where('name', $request->name);
        }

        if($request->has('category_id')){
            $products->whereHas('categories', function ($query) use ($request) {
                $query->where('category_id', $request->category_id);
            });
        }

        if($request->has('category_name')) {
            $category = Category::where('name', $request->category_name)->first();
            $products->whereHas('categories', function ($query) use ($category) {
                $query->where('category_id', $category->id);
            });
        }

        if($request->has('price_min') && $request->has('price_max')) {
            $products->whereBetween('price', [$request->price_min, $request->price_max]);
        }

        if($request->has('only_published')) {
            $products->published();
        }

        if($request->has('not_deleted')) {
            $products->notDeleted();
        }

        $products = $products->withTrashed()->get();

        return new ProductCollection($products);;
    }
    public function store(Request $request)
    {
        $request->validate([
            'categories' => 'min:2|max:10'
        ]);
        $product = Product::create($request->except('categories'));
        foreach ($request->categories as $category){
            ProductCategory::create([
                'product_id' => $product->id,
                'category_id' => $category
            ]);
        }

        return new ProductResource($product);
    }
    public function show(Product $product)
    {
        return new ProductResource($product);
    }
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'categories' => 'min:2|max:10'
        ]);
        $product->update($request->except('categories'));
        ProductCategory::where('product_id', $product->id)->delete();
        foreach ($request->categories as $category){
            ProductCategory::create([
                'product_id' => $product->id,
                'category_id' => $category
            ]);
        }
        return new ProductResource($product);
    }
    public function destroy(Product $product)
    {
        if($product->delete()) return response(null, 204);
    }

}
