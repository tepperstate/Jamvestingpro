<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIAssistantService
{
    public function rewriteContent($content, $prompt = null, $lang = null, $provider = 'gemini', $model = null)
    {
        $defaultInstructions = 'You are an expert financial analyst, SEO specialist, and professional writer for JamVesting Pro, a premium institutional-grade trading platform. You are provided with a news excerpt, title, or short summary. Your task is to write a concise, high-value, direct, and engaging financial article based on this information. The article MUST be between 500 and 1500 words in length, mirroring the style of top financial publishers like Forbes and the Financial Times. It MUST rank on Google. You must use LSI keywords, targeted headings (H2, H3), and deliver deep insights with relevant market context, potential implications for investors, historical comparisons, and a professional, authoritative tone. Do NOT simply rewrite the short text, but expand it to a highly valuable 500-1500 word article. Output HTML tags (like <p>, <h2>, <ul>, <h3>, <strong>) to structure the content beautifully. Do NOT wrap the entire output in a root <html> or <body> tag, just return the raw HTML snippets.';
        $instructions = $prompt ? $prompt.' Output HTML. Write a concise, high-value, direct, and engaging 500-1500 word SEO optimized article.' : $defaultInstructions;
        if ($lang) {
            $instructions .= ' Translate the final output to '.$lang.'.';
        }

        if ($provider === 'round_robin') {
            $available = [];
            if (config('services.gemini.key')) {
                $available[] = 'gemini';
            }
            if (config('services.groq.key')) {
                $available[] = 'groq';
            }
            if (config('services.openrouter.key')) {
                $available[] = 'openrouter';
            }
            if (config('services.copilot.key')) {
                $available[] = 'copilot';
            }

            if (empty($available)) {
                return $this->generateDummyArticle($content);
            }
            $provider = $available[array_rand($available)];
        }

        try {
            // IF NO API KEY CONFIGURED, GENERATE A MASSIVE SEO OPTIMIZED DUMMY ARTICLE
            if (empty(config('services.gemini.key')) && empty(config('services.groq.key')) && empty(config('services.openrouter.key'))) {
                return $this->generateDummyArticle($content);
            }

            if ($provider === 'groq') {
                $apiKey = config('services.groq.key');
                if (! $apiKey) {
                    return $this->generateDummyArticle($content);
                }
                $model = $model ?: 'llama-3.3-70b-versatile';

                $response = Http::withToken($apiKey)->post('https://api.groq.com/openai/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $instructions],
                        ['role' => 'user', 'content' => "Content:\n".strip_tags($content)],
                    ],
                ]);

                if ($response->successful()) {
                    return $this->formatHtml($response->json()['choices'][0]['message']['content'] ?? '');
                }
                throw new \Exception('Groq API Error: '.$response->body());
            } elseif ($provider === 'openrouter') {
                $apiKey = config('services.openrouter.key');
                if (! $apiKey) {
                    return $this->generateDummyArticle($content);
                }
                $model = $model ?: 'mistralai/mixtral-8x7b-instruct';

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                    'HTTP-Referer' => config('app.url'),
                    'X-Title' => config('app.name'),
                ])->post('https://openrouter.ai/api/v1/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $instructions],
                        ['role' => 'user', 'content' => "Content:\n".strip_tags($content)],
                    ],
                ]);

                if ($response->successful()) {
                    return $this->formatHtml($response->json()['choices'][0]['message']['content'] ?? '');
                }
                throw new \Exception('OpenRouter API Error: '.$response->body());
            } elseif ($provider === 'copilot') {
                $apiKey = config('services.copilot.key');
                if (! $apiKey) {
                    return $this->generateDummyArticle($content);
                }
                $model = $model ?: 'gpt-4o';

                $response = Http::withToken($apiKey)->post('https://api.githubcopilot.com/chat/completions', [
                    'model' => $model,
                    'messages' => [
                        ['role' => 'system', 'content' => $instructions],
                        ['role' => 'user', 'content' => "Content:\n".strip_tags($content)],
                    ],
                ]);

                if ($response->successful()) {
                    return $this->formatHtml($response->json()['choices'][0]['message']['content'] ?? '');
                }
                throw new \Exception('Copilot API Error: '.$response->body());
            } else {
                // Default to Gemini
                $apiKey = config('services.gemini.key');
                if (! $apiKey) {
                    return $this->generateDummyArticle($content);
                }
                $model = $model ?: 'gemini-1.5-flash';

                $payload = [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $instructions."\n\nContent:\n".strip_tags($content)],
                            ],
                        ],
                    ],
                ];

                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", $payload);

                if ($response->successful()) {
                    $data = $response->json();
                    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
                        return $this->formatHtml($data['candidates'][0]['content']['parts'][0]['text']);
                    }
                }
                throw new \Exception('Gemini API Error: '.$response->body());
            }
        } catch (\Exception $e) {
            Log::error('AIAssistantService Error: '.$e->getMessage());
        }

        return '';
    }

    private function generateDummyArticle($content)
    {
        $excerpt = strip_tags($content);
        $dummyParagraph = "<p>The overarching paradigm shifts in global macroeconomics demand a closer inspection of institutional frameworks. We have noted that continuous integration of these dynamics accelerates wealth accumulation while concurrently demanding strict risk oversight. Traders and investors must rely on real-time data flows, proprietary algorithms, and disciplined execution. It is clear that the evolving landscape favors those who adopt technology-driven strategies over traditional accumulation models. In understanding the deeper implications, one must factor in liquidity, slippage, margin requirements, and sovereign asset control.</p>\n";

        $longBody = '';
        for ($i = 0; $i < 4; $i++) {
            $longBody .= '<h3>Section '.($i + 1).": Advanced Market Dynamics</h3>\n".$dummyParagraph.$dummyParagraph;
        }

        return <<<HTML
<h2>In-Depth Market Analysis: Deciphering the Latest Trends</h2>
<p>The financial markets are constantly evolving, and recent developments have highlighted significant shifts in both institutional sentiment and retail investor behavior. {$excerpt}</p>
<p>Our analysis indicates that these movements are not merely transient anomalies but part of a broader macroeconomic realignment. Historically, periods of such volatility have preceded major structural changes in asset allocation strategies across global portfolios.</p>
{$longBody}
<h3>Strategic Implications for the Modern Portfolio</h3>
<p>What does this mean for your portfolio? Diversification is no longer sufficient; precision is required. Investors must transition from passive accumulation to active risk management. By leveraging advanced algorithmic tools and deep liquidity pools, such as those provided by JamVesting Pro, traders can execute complex strategies with minimal slippage.</p>
<p>In conclusion, the market's current trajectory underscores the importance of sovereign asset control and rigorous analytical frameworks. Stay informed, stay agile, and continue to leverage institutional-grade intelligence to secure your financial future.</p>
HTML;
    }

    private function formatHtml($text)
    {
        // Clean markdown wrapping if model outputs it
        $text = preg_replace('/```html\n?/', '', $text);
        $text = preg_replace('/```\n?/', '', $text);

        if (strpos($text, '<p>') === false) {
            return '<p>'.preg_replace('/\n\n+/', '</p><p>', trim($text)).'</p>';
        }

        return trim($text);
    }
}
