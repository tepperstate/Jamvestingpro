<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Support\Facades\Cache;

class BlogController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $seed = Cache::remember('blog_random_seed', 3600, function () {
            return rand(1000, 9999);
        });
        
        $query = Blog::query();
        
        if ($request->filled('category')) {
            $query->where('category', $request->category);
            $data = $query->paginate(6)->withQueryString();
        } else {
            $data = $query->inRandomOrder($seed)->paginate(6)->withQueryString();
        }

        $categories = Cache::remember('blog_unique_categories', 3600, function () {
            return Blog::select('category')->whereNotNull('category')->distinct()->pluck('category');
        });

        // Cache posts for random selection in-memory to prevent ORDER BY RAND() (MED-02)
        $featuredPosts = Cache::remember('blog_featured_posts_all', 60, function () {
            return Blog::take(10)->get();
        })->shuffle()->take(2);

        $trendingNews = Blog::latest()->take(4)->get();

        return view('marketing.blog.index', [
            'data' => $data,
            'featuredPosts' => $featuredPosts,
            'trendingNews' => $trendingNews,
            'categories' => $categories,
            'currentCategory' => $request->category,
        ]);
    }

    public function show($slug)
    {
        $data = Blog::where('slug', $slug)->first();
        if (! $data) {
            return redirect()->route('public.blog.index');
        }

        // Cache posts for random selection in-memory to prevent ORDER BY RAND() (MED-02)
        $featuredPosts = Cache::remember('blog_featured_posts_exclude_'.$data->id, 60, function () use ($data) {
            return Blog::where('id', '!=', $data->id)->take(10)->get();
        })->shuffle()->take(2);

        $trendingNews = Blog::where('id', '!=', $data->id)->latest()->take(4)->get();

        $categories = Cache::remember('blog_unique_categories', 3600, function () {
            return Blog::select('category')->whereNotNull('category')->distinct()->pluck('category');
        });

        return view('marketing.blog.single', [
            'data' => $data,
            'featuredPosts' => $featuredPosts,
            'trendingNews' => $trendingNews,
            'categories' => $categories,
        ]);
    }

    public function sitemap()
    {
        $posts = Blog::orderBy('updated_at', 'desc')->get();

        return response()->view('marketing.blog.sitemap', compact('posts'))->header('Content-Type', 'text/xml');
    }
}
