<?php

namespace App\Filament\Pages;

use App\Services\AI\AIManager;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;

class AIProviderTest extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';
    protected static ?string $navigationGroup = 'System';
    protected static ?string $navigationLabel = 'Test AI Providers';
    protected static ?int $navigationSort = 99;
    protected static string $view = 'filament.pages.ai-provider-test';

    public ?array $data = [];
    public ?string $openaiResult = null;
    public ?string $togetherResult = null;
    public ?string $replicateResult = null;
    public ?array $stats = [];

    public function mount(): void
    {
        $this->form->fill([
            'prompt' => 'Explain photosynthesis in simple terms.',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Test AI Providers')
                    ->description('Enter a prompt and compare responses from different AI providers.')
                    ->schema([
                        Textarea::make('prompt')
                            ->label('Test Prompt')
                            ->required()
                            ->rows(3)
                            ->placeholder('Enter a question or instruction...')
                            ->helperText('This prompt will be sent to all available AI providers for comparison.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function testAll(): void
    {
        $this->validate();

        $prompt = $this->data['prompt'];
        $aiManager = app(AIManager::class);

        // Reset results
        $this->openaiResult = null;
        $this->togetherResult = null;
        $this->replicateResult = null;
        $this->stats = [];

        // Test OpenAI
        try {
            $start = microtime(true);
            $response = $aiManager->provider('openai')->complete($prompt, ['max_tokens' => 500]);
            $duration = round((microtime(true) - $start) * 1000, 2);

            $this->openaiResult = $response->content;
            $this->stats['openai'] = [
                'duration' => $duration . 'ms',
                'tokens' => $response->totalTokens,
                'cost' => '$' . number_format($response->cost, 6),
            ];
        } catch (\Exception $e) {
            $this->openaiResult = 'Error: ' . $e->getMessage();
            $this->stats['openai'] = ['error' => true];
            Log::error('OpenAI test failed', ['error' => $e->getMessage()]);
        }

        // Test Together.ai
        try {
            $start = microtime(true);
            $response = $aiManager->provider('together')->complete($prompt, ['max_tokens' => 500]);
            $duration = round((microtime(true) - $start) * 1000, 2);

            $this->togetherResult = $response->content;
            $this->stats['together'] = [
                'duration' => $duration . 'ms',
                'tokens' => $response->totalTokens,
                'cost' => '$' . number_format($response->cost, 6),
            ];
        } catch (\Exception $e) {
            $this->togetherResult = 'Error: ' . $e->getMessage();
            $this->stats['together'] = ['error' => true];
            Log::error('Together.ai test failed', ['error' => $e->getMessage()]);
        }

        // Test Replicate
        try {
            $start = microtime(true);
            $response = $aiManager->provider('replicate')->complete($prompt, ['max_tokens' => 500]);
            $duration = round((microtime(true) - $start) * 1000, 2);

            $this->replicateResult = $response->content;
            $this->stats['replicate'] = [
                'duration' => $duration . 'ms',
                'tokens' => $response->totalTokens,
                'cost' => '$' . number_format($response->cost, 6),
            ];
        } catch (\Exception $e) {
            $this->replicateResult = 'Error: ' . $e->getMessage();
            $this->stats['replicate'] = ['error' => true];
            Log::error('Replicate test failed', ['error' => $e->getMessage()]);
        }

        Notification::make()
            ->title('Testing Complete')
            ->success()
            ->send();
    }
}
