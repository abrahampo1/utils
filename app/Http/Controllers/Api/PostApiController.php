<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;

class PostApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::published()
            ->with(['categories:id,name,slug', 'tags:id,name,slug'])
            ->select(['id', 'title', 'slug', 'excerpt', 'featured_image', 'published_at', 'reading_time'])
            ->latest('published_at');

        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(10));
    }

    public function show(string $slug)
    {
        $post = Post::published()
            ->with(['categories:id,name,slug', 'tags:id,name,slug', 'user:id,name'])
            ->where('slug', $slug)
            ->firstOrFail();

        return response()->json($post);
    }
}
