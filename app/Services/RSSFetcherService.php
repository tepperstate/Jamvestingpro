<?php

namespace App\Services;

use App\Models\Blog;
use App\Models\FeedLog;
use App\Models\FeedSource;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RSSFetcherService
{
    public function fetch(FeedSource $source)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            ])->timeout(30)->get($source->url);

            if (! $response->successful()) {
                throw new Exception('Failed to fetch RSS feed. HTTP Status: '.$response->status());
            }

            $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);

            if (! $xml) {
                throw new Exception('Failed to parse XML.');
            }

            $items = [];
            // Handle standard RSS
            if (isset($xml->channel->item)) {
                $items = $xml->channel->item;
            }
            // Handle Atom
            elseif (isset($xml->entry)) {
                $items = $xml->entry;
            }

            $count = 0;
            $parsedItems = [];
            foreach ($items as $item) {
                if ($count >= $source->import_limit) {
                    break;
                }

                $title = (string) ($item->title ?? '');
                $link = (string) ($item->link ?? '');
                if (isset($item->link['href'])) {
                    $link = (string) $item->link['href'];
                }

                $description = (string) ($item->description ?? $item->summary ?? '');
                $contentEncoded = (string) ($item->children('content', true)->encoded ?? '');
                $content = $contentEncoded !== '' ? $contentEncoded : $description;

                $category = 'General';
                if (isset($item->category)) {
                    if (isset($item->category['term'])) {
                        $category = (string) $item->category['term'];
                    } else {
                        $category = (string) $item->category;
                    }
                }

                // Basic Duplicate Check by URL
                if (Blog::where('slug', Str::slug($title))->exists()) {
                    FeedLog::create([
                        'feed_source_id' => $source->id,
                        'status' => 'duplicate',
                        'message' => 'Skipped duplicate: '.$title,
                        'article_url' => $link,
                    ]);

                    continue;
                }

                // Check Filters
                if (! empty($source->filters) && isset($source->filters['exclude'])) {
                    $exclude = $source->filters['exclude'];
                    $skip = false;
                    foreach ($exclude as $word) {
                        if (stripos($title, $word) !== false || stripos($content, $word) !== false) {
                            $skip = true;
                            break;
                        }
                    }
                    if ($skip) {
                        continue;
                    }
                }

                $parsedItems[] = [
                    'title' => $title,
                    'link' => $link,
                    'content' => $content,
                    'category' => $category,
                    'source_id' => $source->id,
                ];
                $count++;
            }

            return $parsedItems;

        } catch (Exception $e) {
            Log::error('RSS Fetch Error: '.$e->getMessage());
            FeedLog::create([
                'feed_source_id' => $source->id,
                'status' => 'failed',
                'message' => substr($e->getMessage(), 0, 250),
            ]);

            return [];
        }
    }
}
