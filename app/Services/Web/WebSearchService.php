<?php

namespace App\Services\Web;

use App\Services\AI\AIManager;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WebSearchService
{
    private Client $client;
    private ?string $apiKey;
    private AIManager $aiManager;

    public function __construct(AIManager $aiManager)
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);

        $this->apiKey = config('services.search.api_key');
        $this->aiManager = $aiManager;
    }

    /**
     * Search for educational content with AI filtering
     */
    public function search(string $query, int $limit = 10, bool $useAI = true): array
    {
        try {
            // Search using HTML scraping for better results
            $results = $this->searchWithHtmlScraping($query, $limit * 2);

            // Use AI to filter and rank if enabled
            if ($useAI && !empty($results)) {
                $results = $this->filterWithAI($results, $query);
            }

            return array_slice($results, 0, $limit);

        } catch (\Exception $e) {
            Log::error('Web search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Search using HTML scraping (more results than API)
     */
    private function searchWithHtmlScraping(string $query, int $limit): array
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->get('https://html.duckduckgo.com/html/', [
                    'q' => $query,
                ]);

            if (!$response->successful()) {
                return [];
            }

            return $this->parseSearchResults($response->body(), $limit);

        } catch (\Exception $e) {
            Log::error('HTML scraping failed', ['error' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Parse DuckDuckGo HTML results
     */
    private function parseSearchResults(string $html, int $limit): array
    {
        $results = [];

        // Extract links
        preg_match_all('/<a[^>]+class="result__a"[^>]+href="([^"]+)"[^>]*>(.*?)<\/a>/s', $html, $links);
        preg_match_all('/<a[^>]+class="result__snippet"[^>]*>(.*?)<\/a>/s', $html, $snippets);

        $count = min(count($links[1]), $limit);

        for ($i = 0; $i < $count; $i++) {
            $url = html_entity_decode($links[1][$i] ?? '');
            $title = strip_tags($links[2][$i] ?? '');
            $snippet = strip_tags($snippets[1][$i] ?? '');

            // Clean URL
            if (strpos($url, 'uddg=') !== false) {
                parse_str(parse_url($url, PHP_URL_QUERY), $params);
                $url = urldecode($params['uddg'] ?? $url);
            }

            if ($url && $title) {
                $results[] = [
                    'title' => $title,
                    'url' => $url,
                    'snippet' => $snippet,
                    'type' => $this->detectType($url, $title),
                    'source' => 'duckduckgo',
                    'relevance' => 0,
                ];
            }
        }

        return $results;
    }

    /**
     * Detect resource type
     */
    private function detectType(string $url, string $title): string
    {
        $url_lower = strtolower($url);
        $title_lower = strtolower($title);

        if (str_contains($url_lower, '.pdf') || str_contains($title_lower, 'pdf')) {
            return 'pdf';
        }

        if (str_contains($url_lower, 'youtube') || str_contains($url_lower, 'vimeo')) {
            return 'video';
        }

        if (str_contains($title_lower, 'exercise') || str_contains($title_lower, 'ejercicio')) {
            return 'exercise';
        }

        return 'article';
    }

    /**
     * Use AI to filter and rank results
     */
    private function filterWithAI(array $results, string $query): array
    {
        try {
            $resultsText = '';
            foreach ($results as $i => $r) {
                $resultsText .= sprintf("%d. %s\nURL: %s\nSnippet: %s\n\n",
                    $i + 1, $r['title'], $r['url'], $r['snippet']);
            }

            $prompt = <<<PROMPT
Analyze these search results for educational query: "{$query}"

Results:
{$resultsText}

Score each (0-100) for educational relevance and quality. Return JSON:
[{"index":1,"score":85,"reason":"Good resource"},...]

Only include score > 50.
PROMPT;

            $response = $this->aiManager->complete($prompt, ['temperature' => 0.3]);
            $scores = json_decode($response->content, true);

            if ($scores) {
                foreach ($scores as $score) {
                    $idx = ($score['index'] ?? 1) - 1;
                    if (isset($results[$idx])) {
                        $results[$idx]['relevance'] = $score['score'] ?? 0;
                        $results[$idx]['ai_reason'] = $score['reason'] ?? '';
                    }
                }

                $results = array_filter($results, fn($r) => ($r['relevance'] ?? 0) > 50);
                usort($results, fn($a, $b) => ($b['relevance'] ?? 0) <=> ($a['relevance'] ?? 0));
            }

            return array_values($results);

        } catch (\Exception $e) {
            Log::error('AI filtering failed', ['error' => $e->getMessage()]);
            return $results;
        }
    }

    /**
     * Fetch content from URL
     */
    public function fetchContent(string $url): ?string
    {
        try {
            $response = $this->client->get($url, [
                'headers' => [
                    'User-Agent' => 'Mozilla/5.0 (compatible; TeacherPlatform/1.0)',
                ],
            ]);

            return $response->getBody()->getContents();
        } catch (\Exception $e) {
            Log::error('Content fetch failed', [
                'url' => $url,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Search for educational resources
     */
    public function searchEducationalResources(string $topic, string $subject, string $type = 'all'): array
    {
        $typeTerms = match($type) {
            'exercises' => 'ejercicios problems worksheet',
            'pdfs' => 'PDF apuntes notes',
            'videos' => 'video tutorial',
            default => 'educational resources'
        };

        $query = "{$topic} {$subject} {$typeTerms}";

        if ($type === 'pdfs') {
            $query .= ' filetype:pdf';
        }

        return $this->search($query, 20);
    }
}

