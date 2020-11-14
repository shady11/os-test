<?php

namespace App\Http\Controllers;

use App\Exceptions\CategoryAttachedToProduct;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }
    public function store(Request $request)
    {
        $category = Category::create($request->all());
        return $category;
    }
    public function update(Request $request, Category $category)
    {
        $category->update($request->all());
        return $category;
    }
    public function destroy(Category $category)
    {
        if($category->products)
            throw new CategoryAttachedToProduct();
        return response(null, 204);
    }
}
