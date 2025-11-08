<?php

namespace App\Filament\Pages;

use App\Models\Material;
use App\Services\Web\WebSearchService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Log;

class WebSearch extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static ?string $navigationLabel = 'Web Search';

    protected static ?string $title = 'Search Educational Content';

    protected static string $view = 'filament.pages.web-search';

    protected static ?int $navigationSort = 5;

    public ?string $query = '';
    public ?int $subject_id = null;
    public ?int $topic_id = null;
    public array $results = [];
    public array $selectedResults = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Search for Educational Content')
                    ->description('Search the web for educational materials and save them to your library')
                    ->schema([
                        Forms\Components\TextInput::make('query')
                            ->label('Search Query')
                            ->placeholder('e.g., "Introduction to Calculus"')
                            ->required()
                            ->maxLength(255)
                            ->helperText('Enter a topic or keyword to search for educational content'),

                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('subject_id')
                                    ->label('Assign to Subject')
                                    ->relationship('subject', 'name', fn ($query) =>
                                        $query->where('user_id', auth()->id())
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->helperText('Optional: Pre-select subject for saved materials'),

                                Forms\Components\Select::make('topic_id')
                                    ->label('Assign to Topic')
                                    ->relationship('topic', 'name', fn ($query) =>
                                        $query->when($this->subject_id, fn ($q) =>
                                            $q->where('subject_id', $this->subject_id)
                                        )
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->disabled(fn () => !$this->subject_id)
                                    ->helperText('Optional: Pre-select topic for saved materials'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function search(): void
    {
        $this->validate();

        try {
            $webSearchService = app(WebSearchService::class);

            $this->results = $webSearchService->searchEducationalResources(
                $this->query,
                $this->subject_id ? Material::find($this->subject_id)?->name ?? '' : ''
            );

            if (empty($this->results)) {
                Notification::make()
                    ->title('No Results Found')
                    ->warning()
                    ->body('No educational content found for your query. Try different keywords.')
                    ->send();
            } else {
                Notification::make()
                    ->title('Search Complete')
                    ->success()
                    ->body(count($this->results) . ' results found')
                    ->send();
            }

        } catch (\Exception $e) {
            Log::error('Web search failed', [
                'query' => $this->query,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Search Failed')
                ->danger()
                ->body('An error occurred while searching. Please try again.')
                ->send();
        }
    }

    public function saveResult(int $index): void
    {
        if (!isset($this->results[$index])) {
            return;
        }

        $result = $this->results[$index];

        try {
            $material = Material::create([
                'user_id' => auth()->id(),
                'subject_id' => $this->subject_id,
                'topic_id' => $this->topic_id,
                'title' => $result['title'],
                'description' => $result['snippet'] ?? $result['title'],
                'type' => 'link',
                'file_path' => $result['url'],
                'is_processed' => false,
                'ai_metadata' => json_encode([
                    'source' => 'web_search',
                    'search_query' => $this->query,
                    'search_source' => $result['source'] ?? 'unknown',
                ]),
            ]);

            Notification::make()
                ->title('Material Saved')
                ->success()
                ->body("'{$result['title']}' has been added to your materials")
                ->send();

            // Marcar como guardado
            $this->selectedResults[] = $index;

        } catch (\Exception $e) {
            Log::error('Failed to save search result', [
                'result' => $result,
                'error' => $e->getMessage(),
            ]);

            Notification::make()
                ->title('Save Failed')
                ->danger()
                ->body('An error occurred while saving. Please try again.')
                ->send();
        }
    }

    public function saveAllResults(): void
    {
        if (empty($this->results)) {
            return;
        }

        $count = 0;

        foreach ($this->results as $index => $result) {
            if (in_array($index, $this->selectedResults)) {
                continue; // Ya guardado
            }

            try {
                Material::create([
                    'user_id' => auth()->id(),
                    'subject_id' => $this->subject_id,
                    'topic_id' => $this->topic_id,
                    'title' => $result['title'],
                    'description' => $result['snippet'] ?? $result['title'],
                    'type' => 'link',
                    'file_path' => $result['url'],
                    'is_processed' => false,
                    'ai_metadata' => json_encode([
                        'source' => 'web_search',
                        'search_query' => $this->query,
                        'search_source' => $result['source'] ?? 'unknown',
                    ]),
                ]);

                $this->selectedResults[] = $index;
                $count++;

            } catch (\Exception $e) {
                Log::error('Failed to save search result', [
                    'result' => $result,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        Notification::make()
            ->title('Materials Saved')
            ->success()
            ->body("{$count} materials have been added to your library")
            ->send();
    }

    public function clearResults(): void
    {
        $this->results = [];
        $this->selectedResults = [];
        $this->query = '';
    }
}
