<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    //
    public function index()
    {
        $data = Blog::orderByDesc('id')->get();

        return view('admin.blog', [
            'data' => $data,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|in:Course,Research,Webinar,General',
        ]);

        $imageName = 'blog_'.time().'.'.$request->file('image')->getClientOriginalExtension();
        $request->file('image')->storeAs('image', $imageName, 'public');

        Blog::create([
            'image' => $imageName,
            'title' => $request->title,
            'slug' => $this->createSlug($request->title),
            'body' => $request->content,
            'category' => $request->category ?? 'General',
            'author' => auth()->user()->first_name ?? 'Admin',
        ]);

        return back()->with('status', 'Post created successfully.');
    }

    public function delete_blog($id)
    {
        $blog = Blog::findOrFail($id);
        if ($blog->image && Storage::exists('public/image/'.$blog->image)) {
            Storage::delete('public/image/'.$blog->image);
        }
        $blog->delete();

        return back()->with('status', 'Post delete successfully.');
    }

    public function edit_blog($id)
    {
        $data = Blog::findOrFail($id);

        return view('admin.blog_edit', [
            'data' => $data,
        ]);
    }

    public function edit_store(Request $request)
    {
        $request->validate([
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category' => 'nullable|string|in:Course,Research,Webinar,General',
        ]);

        $blog = Blog::findOrFail($request->id);

        $updateData = [
            'title' => $request->title,
            'slug' => $this->createSlug($request->title),
            'body' => $request->content,
            'category' => $request->category ?? 'General',
        ];

        if ($request->hasFile('image')) {
            // Delete old image
            if ($blog->image && Storage::exists('public/image/'.$blog->image)) {
                Storage::delete('public/image/'.$blog->image);
            }

            $imageName = 'blog_'.time().'.'.$request->file('image')->getClientOriginalExtension();
            $request->file('image')->storeAs('image', $imageName, 'public');
            $updateData['image'] = $imageName;
        }

        $blog->update($updateData);

        return back()->with('status', 'Post edited successfully.');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('public/uploads');
            $url = Storage::url($path);

            return response()->json($url);
        }

        return response()->json(['error' => 'No file uploaded'], 400);
    }

    public function createSlug($title)
    {
        // Generate the initial slug
        $slug = Str::slug($title);

        // Check if the slug already exists
        $existingSlugCount = DB::table('blogs')
            ->where('slug', 'LIKE', "$slug%")
            ->count();

        // If there are existing slugs, append the count to the slug
        if ($existingSlugCount > 0) {
            $slug = $slug.'-'.($existingSlugCount + 1);
        }

        return $slug;
    }
}
