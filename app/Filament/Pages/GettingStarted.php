<?php

namespace App\Filament\Pages;

use App\Models\Subject;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class GettingStarted extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationLabel = 'Getting Started';

    protected static ?string $title = 'Welcome! Let\'s Get You Started';

    protected static ?string $navigationGroup = 'Quick Actions';

    protected static ?int $navigationSort = 0;

    protected static string $view = 'filament.pages.getting-started';

    public ?array $data = [];

    public function mount(): void
    {
        // Check if user has already completed onboarding
        if (Auth::user()->subjects()->exists()) {
            Notification::make()
                ->title('You\'re all set!')
                ->body('You\'ve already completed the getting started guide.')
                ->success()
                ->send();

            $this->redirect(route('filament.admin.pages.dashboard'));
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Welcome')
                        ->description('Learn how the platform works')
                        ->icon('heroicon-o-hand-raised')
                        ->schema([
                            Section::make()
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('welcome')
                                        ->content(new \Illuminate\Support\HtmlString('
                                            <div class="space-y-6">
                                                <div class="text-center">
                                                    <div class="inline-flex items-center justify-center w-20 h-20 mb-4 rounded-full bg-gradient-to-br from-primary-500 to-primary-600">
                                                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                        </svg>
                                                    </div>
                                                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                                                        Welcome to Teacher Platform! 👋
                                                    </h2>
                                                    <p class="text-gray-600 dark:text-gray-400 mb-8">
                                                        Your AI-powered learning companion
                                                    </p>
                                                </div>

                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                    <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
                                                        <div class="flex items-center gap-3 mb-2">
                                                            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                                                </svg>
                                                            </div>
                                                            <h3 class="font-semibold text-blue-900 dark:text-blue-100">Upload Documents</h3>
                                                        </div>
                                                        <p class="text-sm text-blue-800 dark:text-blue-200">
                                                            Upload your notes, textbooks, or homework and we\'ll extract the text automatically
                                                        </p>
                                                    </div>

                                                    <div class="p-4 bg-green-50 dark:bg-green-900/20 rounded-lg border border-green-200 dark:border-green-800">
                                                        <div class="flex items-center gap-3 mb-2">
                                                            <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                                </svg>
                                                            </div>
                                                            <h3 class="font-semibold text-green-900 dark:text-green-100">AI-Generated Exercises</h3>
                                                        </div>
                                                        <p class="text-sm text-green-800 dark:text-green-200">
                                                            Get personalized practice questions and flashcards generated from your materials
                                                        </p>
                                                    </div>

                                                    <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg border border-purple-200 dark:border-purple-800">
                                                        <div class="flex items-center gap-3 mb-2">
                                                            <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                                                </svg>
                                                            </div>
                                                            <h3 class="font-semibold text-purple-900 dark:text-purple-100">Smart Flashcards</h3>
                                                        </div>
                                                        <p class="text-sm text-purple-800 dark:text-purple-200">
                                                            Spaced repetition algorithm helps you remember what you learn
                                                        </p>
                                                    </div>

                                                    <div class="p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg border border-yellow-200 dark:border-yellow-800">
                                                        <div class="flex items-center gap-3 mb-2">
                                                            <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                                                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                                </svg>
                                                            </div>
                                                            <h3 class="font-semibold text-yellow-900 dark:text-yellow-100">Track Progress</h3>
                                                        </div>
                                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">
                                                            Earn points, unlock badges, and level up as you learn
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                        '))
                                        ->columnSpanFull(),
                                ])
                        ]),

                    Wizard\Step::make('Create Your First Subject')
                        ->description('Let\'s organize your learning')
                        ->icon('heroicon-o-book-open')
                        ->schema([
                            Section::make()
                                ->schema([
                                    TextInput::make('subject_name')
                                        ->label('Subject Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('e.g., Mathematics, History, Biology')
                                        ->helperText('What subject are you studying?')
                                        ->columnSpanFull(),

                                    TextInput::make('subject_code')
                                        ->label('Subject Code (Optional)')
                                        ->placeholder('e.g., MATH101, HIST201')
                                        ->maxLength(50)
                                        ->columnSpanFull(),

                                    MarkdownEditor::make('subject_description')
                                        ->label('Description (Optional)')
                                        ->placeholder('Brief description of what you\'ll study in this subject...')
                                        ->columnSpanFull(),
                                ])
                        ]),

                    Wizard\Step::make('All Set!')
                        ->description('You\'re ready to start learning')
                        ->icon('heroicon-o-check-circle')
                        ->schema([
                            Section::make()
                                ->schema([
                                    \Filament\Forms\Components\Placeholder::make('complete')
                                        ->content(new \Illuminate\Support\HtmlString('
                                            <div class="text-center space-y-6">
                                                <div class="inline-flex items-center justify-center w-20 h-20 mb-4 rounded-full bg-green-100 dark:bg-green-900">
                                                    <svg class="w-10 h-10 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                                                    You\'re All Set! 🎉
                                                </h2>
                                                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                                                    Your subject has been created. Now you can start uploading materials and generating exercises!
                                                </p>

                                                <div class="pt-6 space-y-3">
                                                    <p class="font-semibold text-gray-900 dark:text-white">Quick Tips:</p>
                                                    <div class="flex items-start gap-3 text-left">
                                                        <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                                            Click "Upload Homework" in the sidebar to add your first document
                                                        </span>
                                                    </div>
                                                    <div class="flex items-start gap-3 text-left">
                                                        <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                                            We\'ll automatically extract text and generate study materials
                                                        </span>
                                                    </div>
                                                    <div class="flex items-start gap-3 text-left">
                                                        <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                        </svg>
                                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                                            Complete exercises to earn points and unlock badges!
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        '))
                                        ->columnSpanFull(),
                                ])
                        ]),
                ])
                ->submitAction(view('filament.pages.getting-started-submit-button'))
                ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        if (!empty($data['subject_name'])) {
            Subject::create([
                'user_id' => Auth::id(),
                'name' => $data['subject_name'],
                'code' => $data['subject_code'] ?? null,
                'description' => $data['subject_description'] ?? null,
            ]);
        }

        Notification::make()
            ->title('Welcome Aboard! 🚀')
            ->body('You\'re ready to start your learning journey!')
            ->success()
            ->send();

        $this->redirect(route('filament.admin.pages.upload-homework'));
    }
}
