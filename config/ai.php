<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default AI Provider
    |--------------------------------------------------------------------------
    |
    | This option controls the default AI provider that will be used by the
    | AI service. You can change this to switch between providers.
    |
    | Supported: "openai", "replicate", "together", "mock"
    |
    */

    'default' => env('AI_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | AI Provider Configurations
    |--------------------------------------------------------------------------
    |
    | Here you may configure the credentials and settings for each AI provider.
    |
    */

    'providers' => [
        'openai' => [
            'api_key' => env('OPENAI_API_KEY'),
            'organization' => env('OPENAI_ORGANIZATION'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'max_tokens' => env('OPENAI_MAX_TOKENS', 2000),
            'temperature' => env('OPENAI_TEMPERATURE', 0.7),
        ],

        'replicate' => [
            'api_key' => env('REPLICATE_API_KEY'),
            'model' => env('REPLICATE_MODEL', 'meta/llama-2-70b-chat'),
            'max_tokens' => env('REPLICATE_MAX_TOKENS', 2000),
            'temperature' => env('REPLICATE_TEMPERATURE', 0.7),
        ],

        'together' => [
            'api_key' => env('TOGETHER_API_KEY'),
            'model' => env('TOGETHER_MODEL', 'meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo'),
            'max_tokens' => env('TOGETHER_MAX_TOKENS', 2000),
            'temperature' => env('TOGETHER_TEMPERATURE', 0.7),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Usage Tracking
    |--------------------------------------------------------------------------
    |
    | Enable or disable token usage tracking and cost calculation.
    |
    */

    'track_usage' => env('AI_TRACK_USAGE', true),

    /*
    |--------------------------------------------------------------------------
    | Monthly Token Limit
    |--------------------------------------------------------------------------
    |
    | Set a monthly limit for AI token usage. Set to null for unlimited.
    |
    */

    'monthly_limit' => env('AI_MONTHLY_LIMIT', 1000000),

    /*
    |--------------------------------------------------------------------------
    | Pricing per 1M tokens (in USD)
    |--------------------------------------------------------------------------
    |
    | Pricing for different models and providers.
    |
    */

    'pricing' => [
        'openai' => [
            'gpt-4o' => ['input' => 2.50, 'output' => 10.00],
            'gpt-4o-mini' => ['input' => 0.15, 'output' => 0.60],
            'gpt-4-turbo' => ['input' => 10.00, 'output' => 30.00],
            'gpt-3.5-turbo' => ['input' => 0.50, 'output' => 1.50],
        ],
        'replicate' => [
            'meta/llama-2-70b-chat' => ['input' => 0.65, 'output' => 2.75],
        ],
        'together' => [
            'meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo' => ['input' => 0.18, 'output' => 0.18],
            'meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo' => ['input' => 0.88, 'output' => 0.88],
        ],
    ],
];
