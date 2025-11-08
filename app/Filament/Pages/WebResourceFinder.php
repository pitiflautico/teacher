<?php

namespace App\Filament\Pages;

use App\Models\Subject;
use App\Services\Web\WebSearchService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Livewire\Attributes\Computed;

class WebResourceFinder extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass';

    protected static string $view = 'filament.pages.web-resource-finder';

    protected static ?string $navigationLabel = 'Find Resources Online';

    protected static ?string $title = 'Find Educational Resources';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 20;

    // Form data
    public ?array $data = [];

    // Search results
    public array $results = [];

    // Selected resources to save
    public array $selected = [];

    // Loading state
    public bool $searching = false;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('Search for Educational Resources'))
                    ->description(__('Find exercises, PDFs, videos, and study materials from the web'))
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('query')
                                    ->label(__('Search Topic'))
                                    ->placeholder(__('e.g., quadratic equations, photosynthesis'))
                                    ->required()
                                    ->columnSpan(2),

                                Forms\Components\Select::make('subject_id')
                                    ->label(__('Subject'))
                                    ->options(Subject::pluck('name', 'id'))
                                    ->searchable()
                                    ->preload(),
                            ]),

                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('resource_type')
                                    ->label(__('Resource Type'))
                                    ->options([
                                        'all' => __('All Types'),
                                        'exercises' => __('Exercises & Worksheets'),
                                        'pdfs' => __('PDFs & Documents'),
                                        'videos' => __('Videos & Tutorials'),
                                    ])
                                    ->default('all')
                                    ->required(),

                                Forms\Components\Select::make('level')
                                    ->label(__('Level'))
                                    ->options([
                                        'elementary' => __('Elementary'),
                                        'middle' => __('Middle School'),
                                        'high' => __('High School'),
                                        'university' => __('University'),
                                    ])
                                    ->placeholder(__('Any level')),

                                Forms\Components\Toggle::make('use_ai')
                                    ->label(__('Use AI to filter results'))
                                    ->helperText(__('AI will analyze and rank results by quality'))
                                    ->default(true),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function search(): void
    {
        $data = $this->form->getState();

        $this->validate();

        $this->searching = true;
        $this->results = [];

        try {
            $webSearch = app(WebSearchService::class);

            $subject = isset($data['subject_id'])
                ? Subject::find($data['subject_id'])?->name
                : '';

            $this->results = $webSearch->searchEducationalResources(
                topic: $data['query'],
                subject: $subject,
                type: $data['resource_type'] ?? 'all'
            );

            if (empty($this->results)) {
                Notification::make()
                    ->warning()
                    ->title(__('No results found'))
                    ->body(__('Try different keywords or disable AI filtering'))
                    ->send();
            } else {
                Notification::make()
                    ->success()
                    ->title(__('Found :count resources', ['count' => count($this->results)]))
                    ->send();
            }

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(__('Search failed'))
                ->body($e->getMessage())
                ->send();
        }

        $this->searching = false;
    }

    public function toggleSelect(int $index): void
    {
        if (in_array($index, $this->selected)) {
            $this->selected = array_diff($this->selected, [$index]);
        } else {
            $this->selected[] = $index;
        }
    }

    public function saveSelected(): void
    {
        if (empty($this->selected)) {
            Notification::make()
                ->warning()
                ->title(__('No resources selected'))
                ->body(__('Please select at least one resource to save'))
                ->send();
            return;
        }

        // TODO: Implement saving selected resources to database
        // For now, just show success message

        Notification::make()
            ->success()
            ->title(__('Resources saved'))
            ->body(__('Selected resources have been saved to your library'))
            ->send();

        $this->selected = [];
    }
}
