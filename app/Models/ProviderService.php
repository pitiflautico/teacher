<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProviderService extends Model
{
    protected $fillable = [
        'user_ai_provider_id',
        'service_type',
        'model',
        'is_active',
        'configuration',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'configuration' => 'array',
    ];

    public function userAiProvider(): BelongsTo
    {
        return $this->belongsTo(UserAiProvider::class);
    }

    /**
     * Get available services for each provider
     */
    public static function getAvailableServices(string $provider): array
    {
        return match($provider) {
            'replicate' => [
                'ocr' => [
                    'label' => 'OCR (Text Recognition)',
                    'description' => 'Extract text from images and documents',
                    'models' => [
                        'salesforce/blip' => 'BLIP - Image Captioning & OCR',
                        'meta/llama-2-70b-chat' => 'Llama 2 70B - Advanced OCR',
                    ],
                ],
                'chat' => [
                    'label' => 'Chat / LLM',
                    'description' => 'Conversational AI and text generation',
                    'models' => [
                        'meta/llama-2-70b-chat' => 'Llama 2 70B Chat',
                        'meta/llama-2-13b-chat' => 'Llama 2 13B Chat',
                    ],
                ],
                'image_recognition' => [
                    'label' => 'Image Recognition',
                    'description' => 'Analyze and understand images',
                    'models' => [
                        'salesforce/blip' => 'BLIP - Image Understanding',
                        'rmokady/clip_prefix_caption' => 'CLIP - Image Analysis',
                    ],
                ],
                'image_generation' => [
                    'label' => 'Image Generation',
                    'description' => 'Create images from text',
                    'models' => [
                        'stability-ai/sdxl' => 'Stable Diffusion XL',
                        'ai-forever/kandinsky-2' => 'Kandinsky 2',
                    ],
                ],
            ],
            'openai' => [
                'chat' => [
                    'label' => 'Chat / GPT',
                    'description' => 'GPT-4 and GPT-3.5 for conversations',
                    'models' => [
                        'gpt-4' => 'GPT-4',
                        'gpt-4-turbo-preview' => 'GPT-4 Turbo',
                        'gpt-3.5-turbo' => 'GPT-3.5 Turbo',
                    ],
                ],
                'image_generation' => [
                    'label' => 'DALL-E',
                    'description' => 'Generate images with DALL-E',
                    'models' => [
                        'dall-e-3' => 'DALL-E 3',
                        'dall-e-2' => 'DALL-E 2',
                    ],
                ],
                'ocr' => [
                    'label' => 'Vision / OCR',
                    'description' => 'GPT-4 Vision for image analysis',
                    'models' => [
                        'gpt-4-vision-preview' => 'GPT-4 Vision',
                    ],
                ],
            ],
            'anthropic' => [
                'chat' => [
                    'label' => 'Claude',
                    'description' => 'Claude AI for conversations',
                    'models' => [
                        'claude-3-opus' => 'Claude 3 Opus',
                        'claude-3-sonnet' => 'Claude 3 Sonnet',
                        'claude-3-haiku' => 'Claude 3 Haiku',
                    ],
                ],
                'image_recognition' => [
                    'label' => 'Vision',
                    'description' => 'Claude with vision capabilities',
                    'models' => [
                        'claude-3-opus' => 'Claude 3 Opus (Vision)',
                        'claude-3-sonnet' => 'Claude 3 Sonnet (Vision)',
                    ],
                ],
            ],
            'google' => [
                'chat' => [
                    'label' => 'Gemini',
                    'description' => 'Gemini AI for conversations',
                    'models' => [
                        'gemini-pro' => 'Gemini Pro',
                        'gemini-ultra' => 'Gemini Ultra',
                    ],
                ],
                'image_recognition' => [
                    'label' => 'Gemini Vision',
                    'description' => 'Multimodal AI',
                    'models' => [
                        'gemini-pro-vision' => 'Gemini Pro Vision',
                    ],
                ],
            ],
            'together' => [
                'chat' => [
                    'label' => 'Chat / LLM',
                    'description' => 'Fast inference with Llama models',
                    'models' => [
                        'meta-llama/Meta-Llama-3.1-8B-Instruct-Turbo' => 'Llama 3.1 8B Turbo',
                        'meta-llama/Meta-Llama-3.1-70B-Instruct-Turbo' => 'Llama 3.1 70B Turbo',
                    ],
                ],
            ],
            default => [],
        };
    }

    /**
     * Get service label
     */
    public function getServiceLabelAttribute(): string
    {
        $services = self::getAvailableServices($this->userAiProvider->provider);
        return $services[$this->service_type]['label'] ?? ucfirst(str_replace('_', ' ', $this->service_type));
    }
}
