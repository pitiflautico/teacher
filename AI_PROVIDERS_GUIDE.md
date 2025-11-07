# 🤖 AI PROVIDERS INTEGRATION GUIDE

## Overview

The Teacher Platform supports **3 AI providers** for different tasks:
1. **OpenAI** (GPT-4o-mini) - Fastest, most accurate
2. **Replicate** (Llama 2 & other models) - Open-source models
3. **Together.ai** (Llama 3.1 & many models) - Fast, cost-effective, open-source

---

## 1️⃣ TOGETHER.AI (Recommended)

### Why Together.ai?
- ✅ **Fast inference** (optimized for production)
- ✅ **Cost-effective** ($0.20-$0.90 per 1M tokens)
- ✅ **100+ open-source models** (Llama, Mixtral, Qwen, etc.)
- ✅ **OpenAI-compatible API** (easy migration)
- ✅ **No cold starts** (always ready)

### Official Documentation:
- Website: https://www.together.ai/
- Docs: https://docs.together.ai/
- API Reference: https://docs.together.ai/reference/
- Models List: https://docs.together.ai/docs/models

### Setup:
```bash
# Install Together PHP Client (if needed)
composer require guzzlehttp/guzzle

# Add to .env
TOGETHER_API_KEY=your_api_key_here
TOGETHER_MODEL=meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo
```

### Get API Key:
1. Go to https://api.together.xyz/
2. Sign up / Log in
3. Navigate to Settings → API Keys
4. Create new API key
5. Copy to .env

### Available Models:

#### **Recommended for Education:**

**1. Llama 3.1 70B Instruct Turbo** (Best overall)
```
Model ID: meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo
Context: 8,192 tokens
Cost: $0.88 per 1M input, $0.88 per 1M output
Use for: Exercise generation, explanations, summaries
```

**2. Llama 3.1 8B Instruct Turbo** (Fastest, cheapest)
```
Model ID: meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo
Context: 8,192 tokens
Cost: $0.20 per 1M input, $0.20 per 1M output
Use for: Simple Q&A, flashcard generation, quick responses
```

**3. Mixtral 8x7B Instruct** (Good balance)
```
Model ID: mistralai/Mixtral-8x7B-Instruct-v0.1
Context: 32,768 tokens
Cost: $0.60 per 1M input, $0.60 per 1M output
Use for: Complex reasoning, long contexts
```

**4. Qwen 2.5 72B Instruct** (Multilingual)
```
Model ID: Qwen/Qwen2.5-72B-Instruct-Turbo
Context: 32,768 tokens
Cost: $0.90 per 1M input, $0.90 per 1M output
Use for: Multilingual education (Spanish/English/Chinese/etc.)
```

### Implementation Example:

```php
<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TogetherAIService
{
    protected $apiKey;
    protected $baseUrl = 'https://api.together.xyz/v1';
    protected $model;

    public function __construct()
    {
        $this->apiKey = config('services.together.api_key');
        $this->model = config('services.together.model', 'meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo');
    }

    /**
     * Generate text completion
     */
    public function complete(string $prompt, array $options = []): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ])->post($this->baseUrl . '/chat/completions', [
            'model' => $options['model'] ?? $this->model,
            'messages' => [
                ['role' => 'system', 'content' => $options['system'] ?? 'You are a helpful educational assistant.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'max_tokens' => $options['max_tokens'] ?? 2000,
            'temperature' => $options['temperature'] ?? 0.7,
            'top_p' => $options['top_p'] ?? 0.9,
            'stop' => $options['stop'] ?? null,
        ]);

        if (!$response->successful()) {
            throw new \Exception('Together.ai API error: ' . $response->body());
        }

        return $response->json()['choices'][0]['message']['content'];
    }

    /**
     * Generate exercise from material
     */
    public function generateExercise(string $materialText, string $difficulty = 'medium'): array
    {
        $prompt = "Based on the following educational material, create 1 multiple-choice question with 4 options.

Material:
{$materialText}

Difficulty: {$difficulty}

Format the response as JSON:
{
  \"question\": \"Question text here?\",
  \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
  \"correct_answer\": \"Option A\",
  \"explanation\": \"Why this is correct\"
}";

        $response = $this->complete($prompt, [
            'system' => 'You are an expert educator creating high-quality assessment questions. Always respond with valid JSON only.',
            'temperature' => 0.5, // Lower for more consistent JSON
        ]);

        // Parse JSON response
        $json = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse AI response as JSON');
        }

        return $json;
    }

    /**
     * Generate flashcards from topic
     */
    public function generateFlashcards(string $topic, int $count = 5): array
    {
        $prompt = "Create {$count} educational flashcards about: {$topic}

Format as JSON array:
[
  {\"front\": \"Question or term\", \"back\": \"Answer or definition\"},
  {\"front\": \"...\", \"back\": \"...\"}
]";

        $response = $this->complete($prompt, [
            'system' => 'You are creating flashcards for spaced repetition study. Be concise and clear. Respond with valid JSON only.',
            'temperature' => 0.6,
            'max_tokens' => 1500,
        ]);

        return json_decode($response, true);
    }

    /**
     * Explain a concept
     */
    public function explainConcept(string $concept, string $level = 'beginner'): string
    {
        $prompt = "Explain the following concept in a clear, educational way for a {$level} level student:\n\n{$concept}";

        return $this->complete($prompt, [
            'system' => 'You are a patient, friendly teacher. Use simple language, examples, and analogies.',
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);
    }

    /**
     * Generate study summary
     */
    public function summarizeMaterial(string $text, int $maxLength = 500): string
    {
        $prompt = "Summarize the following educational material in approximately {$maxLength} words. Focus on key concepts:\n\n{$text}";

        return $this->complete($prompt, [
            'system' => 'You create concise, accurate study summaries that highlight the most important information.',
            'temperature' => 0.4,
            'max_tokens' => $maxLength * 2,
        ]);
    }

    /**
     * List available models
     */
    public function listModels(): array
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])->get($this->baseUrl . '/models');

        if (!$response->successful()) {
            throw new \Exception('Failed to fetch models');
        }

        return $response->json()['data'];
    }
}
```

### Configuration (config/services.php):
```php
'together' => [
    'api_key' => env('TOGETHER_API_KEY'),
    'model' => env('TOGETHER_MODEL', 'meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo'),
],
```

---

## 2️⃣ REPLICATE (Open Source Models)

### Why Replicate?
- ✅ **Huge model catalog** (thousands of models)
- ✅ **Easy model switching** (no infrastructure management)
- ✅ **Pay per second** (no monthly fees)
- ✅ **Custom model hosting** (can upload your own)
- ✅ **GPU-accelerated** (fast inference)

### Official Documentation:
- Website: https://replicate.com/
- Docs: https://replicate.com/docs
- API Reference: https://replicate.com/docs/reference/http
- Models: https://replicate.com/explore

### Setup:
```bash
# Install Replicate PHP Client
composer require replicate/replicate-php

# Add to .env
REPLICATE_API_KEY=r8_your_api_key_here
REPLICATE_MODEL=meta/llama-2-70b-chat
```

### Get API Key:
1. Go to https://replicate.com/
2. Sign up / Log in
3. Navigate to Account → API tokens
4. Create new token
5. Copy to .env

### Available Models for Education:

**1. Llama 2 70B Chat** (Great for conversations)
```
Model: meta/llama-2-70b-chat
Version: latest
Use for: Q&A, explanations, tutoring
```

**2. Llama 2 13B Chat** (Faster, cheaper)
```
Model: meta/llama-2-13b-chat
Version: latest
Use for: Simple questions, summaries
```

**3. Mistral 7B Instruct** (Excellent performance)
```
Model: mistralai/mistral-7b-instruct-v0.2
Version: latest
Use for: General education tasks
```

**4. Mixtral 8x7B** (High quality)
```
Model: mistralai/mixtral-8x7b-instruct-v0.1
Version: latest
Use for: Complex reasoning
```

**5. Qwen 72B** (Multilingual)
```
Model: qwen/qwen-72b-chat
Version: latest
Use for: International education
```

### Implementation Example:

```php
<?php

namespace App\Services;

use Replicate\Replicate;

class ReplicateAIService
{
    protected $client;
    protected $model;

    public function __construct()
    {
        $this->client = new Replicate(config('services.replicate.api_key'));
        $this->model = config('services.replicate.model', 'meta/llama-2-70b-chat');
    }

    /**
     * Generate text completion
     */
    public function complete(string $prompt, array $options = []): string
    {
        $model = $this->client->model($options['model'] ?? $this->model);
        
        $response = $model->predict([
            'prompt' => $prompt,
            'max_new_tokens' => $options['max_tokens'] ?? 2000,
            'temperature' => $options['temperature'] ?? 0.7,
            'top_p' => $options['top_p'] ?? 0.9,
            'repetition_penalty' => $options['repetition_penalty'] ?? 1,
        ]);

        // Response is an array of strings, join them
        if (is_array($response)) {
            return implode('', $response);
        }

        return $response;
    }

    /**
     * Generate exercise with Llama 2
     */
    public function generateExercise(string $materialText, string $difficulty = 'medium'): array
    {
        $prompt = "[INST] Based on the following educational material, create 1 multiple-choice question with 4 options.

Material:
{$materialText}

Difficulty: {$difficulty}

Respond ONLY with valid JSON in this exact format:
{
  \"question\": \"Question text here?\",
  \"options\": [\"Option A\", \"Option B\", \"Option C\", \"Option D\"],
  \"correct_answer\": \"Option A\",
  \"explanation\": \"Why this is correct\"
} [/INST]";

        $response = $this->complete($prompt, [
            'temperature' => 0.4, // Lower for more structured output
            'max_tokens' => 1000,
        ]);

        // Extract JSON from response
        preg_match('/\{[^}]+\}/', $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Failed to extract JSON from AI response');
        }

        $json = json_decode($matches[0], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception('Failed to parse AI response as JSON');
        }

        return $json;
    }

    /**
     * Generate flashcards
     */
    public function generateFlashcards(string $topic, int $count = 5): array
    {
        $prompt = "[INST] Create {$count} educational flashcards about: {$topic}

Respond ONLY with valid JSON array:
[
  {\"front\": \"Question or term\", \"back\": \"Answer or definition\"},
  {\"front\": \"...\", \"back\": \"...\"}
] [/INST]";

        $response = $this->complete($prompt, [
            'temperature' => 0.5,
            'max_tokens' => 1500,
        ]);

        // Extract JSON array
        preg_match('/\[.*\]/s', $response, $matches);
        if (empty($matches)) {
            throw new \Exception('Failed to extract JSON array');
        }

        return json_decode($matches[0], true);
    }

    /**
     * Explain concept
     */
    public function explainConcept(string $concept, string $level = 'beginner'): string
    {
        $prompt = "[INST] Explain the following concept in a clear, educational way for a {$level} level student:

{$concept} [/INST]";

        return $this->complete($prompt, [
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ]);
    }

    /**
     * Use image models (e.g., for diagram generation)
     */
    public function generateDiagram(string $description): string
    {
        $model = $this->client->model('stability-ai/sdxl');
        
        $response = $model->predict([
            'prompt' => "Educational diagram: {$description}. Simple, clear, labeled.",
            'negative_prompt' => 'complex, cluttered, text-heavy',
        ]);

        // Returns URL to generated image
        return $response[0];
    }

    /**
     * List available models
     */
    public function listModels(string $query = ''): array
    {
        // Note: This requires Replicate API client methods
        // For now, recommend checking https://replicate.com/explore
        return [];
    }
}
```

### Configuration (config/services.php):
```php
'replicate' => [
    'api_key' => env('REPLICATE_API_KEY'),
    'model' => env('REPLICATE_MODEL', 'meta/llama-2-70b-chat'),
],
```

---

## 3️⃣ OPENAI (GPT-4o-mini)

### Already Configured
The platform already has OpenAI configured. See existing implementation in:
- `app/Services/AIService.php`
- Uses GPT-4o-mini for exercise generation

---

## 📊 COMPARISON TABLE

| Feature | Together.ai | Replicate | OpenAI |
|---------|------------|-----------|--------|
| **Speed** | ⚡⚡⚡ Very Fast | ⚡⚡ Fast | ⚡⚡ Fast |
| **Cost** | 💰 $0.20-$0.90/1M | 💰💰 Pay per second | 💰💰💰 $0.15-$5/1M |
| **Models** | 100+ open-source | 1000+ models | GPT-3.5, GPT-4 |
| **Ease of Use** | ⭐⭐⭐⭐⭐ OpenAI compatible | ⭐⭐⭐⭐ Simple API | ⭐⭐⭐⭐⭐ Best docs |
| **Quality** | ⭐⭐⭐⭐ Excellent | ⭐⭐⭐⭐ Excellent | ⭐⭐⭐⭐⭐ Best |
| **Latency** | ~500ms | ~1-2s (cold start) | ~500ms |
| **Best For** | Production, Scale | Experimentation | Critical tasks |

---

## 🎯 RECOMMENDED USAGE

### **Primary: Together.ai (Llama 3.1 70B)**
Use for:
- Exercise generation
- Flashcard creation
- Material summaries
- Concept explanations
- General Q&A

### **Secondary: Replicate (Llama 2 70B)**
Use for:
- Backup when Together.ai is down
- Specific models not on Together.ai
- Image generation (SDXL)
- Experimentation

### **Tertiary: OpenAI (GPT-4o-mini)**
Use for:
- Critical tasks requiring highest accuracy
- Complex reasoning
- When budget allows

---

## 🔧 INTEGRATION STEPS

### 1. Add to .env:
```bash
# Together.ai
TOGETHER_API_KEY=your_key
TOGETHER_MODEL=meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo

# Replicate
REPLICATE_API_KEY=your_key
REPLICATE_MODEL=meta/llama-2-70b-chat
```

### 2. Create Service Files:
- `app/Services/TogetherAIService.php` (see above)
- `app/Services/ReplicateAIService.php` (see above)

### 3. Update AIService.php to use preferred provider:
```php
public function generateExercise($material)
{
    $provider = auth()->user()->profile->preferred_ai_provider ?? 'together';
    
    switch ($provider) {
        case 'together':
            $service = app(TogetherAIService::class);
            break;
        case 'replicate':
            $service = app(ReplicateAIService::class);
            break;
        default:
            $service = app(OpenAIService::class);
    }
    
    return $service->generateExercise($material->extracted_text);
}
```

### 4. Test:
```bash
php artisan tinker

$together = app(\App\Services\TogetherAIService::class);
$result = $together->explainConcept('Photosynthesis', 'beginner');
echo $result;

$replicate = app(\App\Services\ReplicateAIService::class);
$flashcards = $replicate->generateFlashcards('Spanish verbs', 5);
print_r($flashcards);
```

---

## 💡 TIPS

1. **Rate Limiting**: Implement caching to avoid repeated calls
2. **Error Handling**: Always wrap API calls in try-catch
3. **Fallback**: If primary provider fails, try secondary
4. **Cost Tracking**: Log token usage in `token_usages` table
5. **User Choice**: Let users select preferred provider in their profile
6. **Testing**: Use smaller models (8B) for development
7. **Async**: Use queues for long-running AI tasks

---

## 📚 ADDITIONAL RESOURCES

- **Together.ai Discord**: https://discord.gg/together
- **Replicate Discord**: https://discord.gg/replicate
- **Llama 3.1 Guide**: https://ai.meta.com/llama/
- **Prompt Engineering**: https://www.promptingguide.ai/

---

**Status**: ✅ Ready to integrate  
**Recommendation**: Start with Together.ai for best balance of speed, cost, and quality
