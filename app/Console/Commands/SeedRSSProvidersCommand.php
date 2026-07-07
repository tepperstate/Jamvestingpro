<?php

namespace App\Console\Commands;

use App\Models\FeedSource;
use Illuminate\Console\Command;

class SeedRSSProvidersCommand extends Command
{
    protected $signature = 'db:seed-rss';

    protected $description = 'Seed the database with default RSS feed providers';

    public function handle()
    {
        $feeds = [
            // Top Broad Financial News Publishers
            ['name' => 'CNBC Top News', 'url' => 'https://search.cnbc.com/rs/search/combinedcms/view.xml?id=100003114', 'category' => 'Top Broad Financial News Publishers'],
            ['name' => 'CNBC Investing', 'url' => 'https://search.cnbc.com/rs/search/combinedcms/view.xml?id=10000664', 'category' => 'Top Broad Financial News Publishers'],
            ['name' => 'CNBC Economy', 'url' => 'https://search.cnbc.com/rs/search/combinedcms/view.xml?id=20910258', 'category' => 'Top Broad Financial News Publishers'],
            ['name' => 'WSJ Markets News', 'url' => 'https://feeds.a.dj.com/rss/RSSMarketsMain.xml', 'category' => 'Top Broad Financial News Publishers'],
            ['name' => 'WSJ Business', 'url' => 'https://feeds.a.dj.com/rss/WSJcomUSBusiness.xml', 'category' => 'Top Broad Financial News Publishers'],
            ['name' => 'WSJ Technology', 'url' => 'https://feeds.a.dj.com/rss/RSSWSJD.xml', 'category' => 'Top Broad Financial News Publishers'],
            ['name' => 'MarketWatch Top Stories', 'url' => 'http://feeds.marketwatch.com/marketwatch/topstories', 'category' => 'Top Broad Financial News Publishers'],
            ['name' => 'MarketWatch MarketPulse', 'url' => 'http://feeds.marketwatch.com/marketwatch/marketpulse', 'category' => 'Top Broad Financial News Publishers'],
            ['name' => 'MarketWatch Real Estate', 'url' => 'http://feeds.marketwatch.com/marketwatch/realestate', 'category' => 'Top Broad Financial News Publishers'],

            // Market Data & Investment Focus
            ['name' => 'Investing.com Top News', 'url' => 'https://www.investing.com/rss/news.rss', 'category' => 'Market Data & Investment Focus'],
            ['name' => 'Investing.com Market Overview', 'url' => 'https://www.investing.com/rss/market_overview.rss', 'category' => 'Market Data & Investment Focus'],
            ['name' => 'Investing.com Forex News', 'url' => 'https://www.investing.com/rss/news_1.rss', 'category' => 'Market Data & Investment Focus'],
            ['name' => 'Investing.com Stock Market News', 'url' => 'https://www.investing.com/rss/news_25.rss', 'category' => 'Market Data & Investment Focus'],
            ['name' => 'Nasdaq Main News Feed', 'url' => 'https://www.nasdaq.com/feed/rssoutbound', 'category' => 'Market Data & Investment Focus'],

            // Cryptocurrency & Decentralized Finance
            ['name' => 'Cointelegraph Main News', 'url' => 'https://cointelegraph.com/rss', 'category' => 'Cryptocurrency & Decentralized Finance'],
            ['name' => 'CoinDesk Main News', 'url' => 'https://www.coindesk.com/arc/outboundfeeds/rss/', 'category' => 'Cryptocurrency & Decentralized Finance'],

            // Personal Finance & Wealth Management
            ['name' => 'Kiplinger Main Feed', 'url' => 'https://www.kiplinger.com/rss', 'category' => 'Personal Finance & Wealth Management'],
        ];

        foreach ($feeds as $feed) {
            FeedSource::updateOrCreate(
                ['url' => $feed['url']],
                [
                    'name' => $feed['name'],
                    'category' => $feed['category'],
                    'active' => true,
                    'cron_schedule' => '4_hours',
                    'import_limit' => 5,
                    'ai_provider' => 'round_robin',
                ]
            );
        }

        $this->info('Successfully seeded RSS feed providers.');
    }
}
