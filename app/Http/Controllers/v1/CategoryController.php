<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     *
     * @return Collection
     */
    public function index(Request $request): Collection
    {
        $query = Category::query();

        if ($request->q) {
            $query->where('name', 'like', $request->q);
        }

        return $query->get();
    }

    /**
     * Display the specified resource.
     *
     * @param Category $category
     *
     * @return Category
     */
    public function show(Category $category): Category
    {
        return $category;
    }
}
