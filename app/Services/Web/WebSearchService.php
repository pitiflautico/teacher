<?php

namespace App\Services\Web;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class WebSearchService
{
    private Client $client;
    private ?string $apiKey;

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 30,
            'verify' => false,
        ]);

        $this->apiKey = config('services.search.api_key');
    }

    /**
     * Search for educational content
     */
    public function search(string $query, int $limit = 10): array
    {
        try {
            // Using DuckDuckGo API (no API key required)
            $response = $this->client->get('https://api.duckduckgo.com/', [
                'query' => [
                    'q' => $query,
                    'format' => 'json',
                    'no_html' => 1,
                    't' => 'teacher_platform',
                ]
            ]);

            $data = json_decode($response->getBody()->getContents(), true);

            $results = [];

            // Process related topics
            if (isset($data['RelatedTopics'])) {
                foreach (array_slice($data['RelatedTopics'], 0, $limit) as $topic) {
                    if (isset($topic['Text']) && isset($topic['FirstURL'])) {
                        $results[] = [
                            'title' => $topic['Text'],
                            'url' => $topic['FirstURL'],
                            'snippet' => $topic['Text'],
                            'source' => 'duckduckgo',
                        ];
                    }
                }
            }

            return $results;
        } catch (\Exception $e) {
            Log::error('Web search failed', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
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
    public function searchEducationalResources(string $topic, string $subject): array
    {
        $query = "{$topic} {$subject} educational resources study material";

        return $this->search($query, 20);
    }
}
