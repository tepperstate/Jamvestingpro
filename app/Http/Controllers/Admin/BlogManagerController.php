<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessFeedImportJob;
use App\Models\Blog;
use App\Models\FeedSource;
use Illuminate\Http\Request;

class BlogManagerController extends Controller
{
    public function index()
    {
        $sources = FeedSource::withCount('logs')->get();
        $categories = Blog::select('category')->distinct()->pluck('category')->filter()->values();

        return view('admin.blog-manager.index', compact('sources', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'url' => 'required|url',
            'category' => 'nullable|string',
            'cron_schedule' => 'required|string',
            'import_limit' => 'required|integer',
            'ai_prompt' => 'nullable|string',
            'translation_lang' => 'nullable|string',
            'ai_provider' => 'required|string',
            'ai_model' => 'nullable|string',
        ]);

        FeedSource::create($validated);

        return back()->with('status', 'Feed Source Added Successfully!');
    }

    public function update(Request $request)
    {
        $source = FeedSource::findOrFail($request->id);
        $source->update($request->except(['_token', 'id']));

        return back()->with('status', 'Feed Source Updated!');
    }

    public function destroy($id)
    {
        FeedSource::findOrFail($id)->delete();

        return back()->with('status', 'Feed Source Deleted!');
    }

    public function forceSync($id)
    {
        $source = FeedSource::findOrFail($id);
        // Dispatch job immediately instead of queueing for the demo
        ProcessFeedImportJob::dispatchSync($source->id);

        return back()->with('status', 'Force Sync Completed! The AI has processed the feeds.');
    }
}
