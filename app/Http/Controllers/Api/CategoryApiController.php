<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryApiController extends Controller
{
    public function index()
    {
        $categories = Category::withCount(['posts' => function ($query) {
            $query->published();
        }])->orderBy('name')->get(['id', 'name', 'slug', 'description']);

        return response()->json($categories);
    }
}
