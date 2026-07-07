<?php

namespace App\Jobs;

use App\Models\Blog;
use App\Models\FeedLog;
use App\Models\FeedSource;
use App\Services\AIAssistantService;
use App\Services\RSSFetcherService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProcessFeedImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $sourceId;

    public function __construct($sourceId)
    {
        $this->sourceId = $sourceId;
    }

    public function handle(RSSFetcherService $fetcher, AIAssistantService $ai)
    {
        $source = FeedSource::find($this->sourceId);
        if (! $source || ! $source->active) {
            return;
        }

        $items = $fetcher->fetch($source);

        foreach ($items as $item) {
            if (Blog::where('slug', Str::slug($item['title']))->exists()) {
                continue;
            }

            $contentToRewrite = 'Title: '.$item['title']."\n\nExcerpt: ".$item['content'];
            $newContent = $ai->rewriteContent($contentToRewrite, $source->ai_prompt, $source->translation_lang, $source->ai_provider, $source->ai_model);

            if (empty(trim(strip_tags($newContent)))) {
                continue;
            }

            $newTitle = $item['title'];

            // Handle Category
            $category = $source->category;
            if (empty($category) || strtolower($category) === 'auto') {
                $category = $item['category'] ?? 'General';
            }
            if (empty($category)) {
                $category = 'General';
            }

            // Determine category-specific fallback image
            $categoryImages = [
                'Course' => 'cat_course.jpg',
                'Research' => 'cat_research.jpg',
                'Webinar' => 'cat_webinar.jpg',
                'Top Broad Financial News Publishers' => 'cat_broad_news.jpg',
                'Market Data & Investment Focus' => 'cat_market_data.jpg',
                'Cryptocurrency & Decentralized Finance' => 'cat_crypto.jpg',
                'Personal Finance & Wealth Management' => 'cat_personal_finance.jpg',
            ];
            $defaultImage = $categoryImages[$category] ?? 'default_blog.jpg';

            // Image Generation with free AI (Pollinations.ai)
            $imageFilename = $defaultImage;
            try {
                $imagePrompt = urlencode('High quality, modern, professional blog post cover image about: '.$newTitle);
                $imageUrl = "https://image.pollinations.ai/prompt/{$imagePrompt}?width=800&height=400&nologo=true";

                $imageResponse = Http::timeout(15)->get($imageUrl);

                if ($imageResponse->successful() && str_contains($imageResponse->header('Content-Type'), 'image')) {
                    $filename = Str::limit(Str::slug($newTitle), 50, '').'-'.time().'.jpg';
                    Storage::disk('public')->put('image/'.$filename, $imageResponse->body());
                    $imageFilename = $filename;
                }
            } catch (\Exception $e) {
                // Keep default_blog.jpg
            }

            $blog = new Blog;
            $blog->title = $newTitle;
            $blog->slug = Str::slug($newTitle);
            $blog->category = $category;
            $blog->body = $newContent;
            $blog->image = $imageFilename;
            $siteName = env('APP_NAME', 'Site');
            $blog->author = $siteName.' Research Team';
            $blog->save();

            FeedLog::create([
                'feed_source_id' => $source->id,
                'status' => 'success',
                'message' => 'Imported and AI-rewritten successfully.',
                'article_url' => $item['link'],
            ]);

            sleep(4);
        }
    }
}
