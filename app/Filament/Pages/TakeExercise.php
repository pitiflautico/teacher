<?php

namespace App\Filament\Pages;

use App\Models\Exercise;
use App\Models\ExerciseAttempt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class TakeExercise extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static string $view = 'filament.pages.take-exercise';

    protected static ?string $navigationLabel = 'Do Exercises';

    protected static ?string $title = 'Do Exercises';

    protected static ?string $navigationGroup = 'Learning';

    protected static ?int $navigationSort = 1;

    public ?Exercise $exercise = null;
    public ?array $data = [];
    public ?int $startTime = null;

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('estudiante');
    }

    public function mount(): void
    {
        // Get a random active exercise that user hasn't completed recently
        $this->exercise = Exercise::query()
            ->where('is_active', true)
            ->whereDoesntHave('attempts', function ($query) {
                $query->where('user_id', auth()->id())
                    ->where('is_correct', true)
                    ->where('created_at', '>', now()->subDays(7));
            })
            ->inRandomOrder()
            ->first();

        $this->startTime = time();

        if (!$this->exercise) {
            $this->exercise = Exercise::where('is_active', true)->inRandomOrder()->first();
        }

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        if (!$this->exercise) {
            return $form->schema([
                Forms\Components\Placeholder::make('no_exercises')
                    ->label('')
                    ->content('No exercises available at the moment.'),
            ]);
        }

        $schema = [
            Forms\Components\Section::make('Exercise Information')
                ->schema([
                    Forms\Components\Placeholder::make('subject')
                        ->label('Subject')
                        ->content($this->exercise->subject?->name ?? 'General'),
                    Forms\Components\Placeholder::make('topic')
                        ->label('Topic')
                        ->content($this->exercise->topic?->name ?? 'General'),
                    Forms\Components\Placeholder::make('difficulty')
                        ->label('Difficulty')
                        ->content(ucfirst($this->exercise->difficulty))
                        ->extraAttributes(['class' => 'font-bold']),
                    Forms\Components\Placeholder::make('points')
                        ->label('Points')
                        ->content($this->exercise->points),
                ])->columns(4),

            Forms\Components\Section::make('Question')
                ->schema([
                    Forms\Components\Placeholder::make('title')
                        ->label('')
                        ->content(fn () => new \Illuminate\Support\HtmlString(
                            '<h2 class="text-xl font-bold mb-4">' . $this->exercise->title . '</h2>' .
                            '<div class="prose dark:prose-invert max-w-none">' . $this->exercise->question . '</div>'
                        )),
                ]),
        ];

        // Add answer field based on exercise type
        $answerField = match ($this->exercise->type) {
            'multiple_choice' => Forms\Components\Radio::make('answer')
                ->label('Select your answer')
                ->options($this->exercise->options ?? [])
                ->required(),

            'true_false' => Forms\Components\Radio::make('answer')
                ->label('Select your answer')
                ->options([
                    'true' => 'True',
                    'false' => 'False',
                ])
                ->required(),

            'short_answer' => Forms\Components\TextInput::make('answer')
                ->label('Your answer')
                ->required()
                ->maxLength(255),

            'essay' => Forms\Components\RichEditor::make('answer')
                ->label('Your answer')
                ->required()
                ->columnSpanFull(),

            'problem_solving' => Forms\Components\Textarea::make('answer')
                ->label('Your solution (show your work)')
                ->required()
                ->rows(10)
                ->columnSpanFull(),

            default => Forms\Components\TextInput::make('answer')
                ->label('Your answer')
                ->required(),
        };

        $schema[] = Forms\Components\Section::make('Your Answer')
            ->schema([$answerField]);

        return $form->schema($schema);
    }

    public function submit(): void
    {
        if (!$this->exercise) {
            Notification::make()
                ->title('No exercise available')
                ->warning()
                ->send();
            return;
        }

        $data = $this->form->getState();
        $userAnswer = is_array($data['answer']) ? $data['answer'] : [$data['answer']];

        $isCorrect = $this->exercise->checkAnswer($userAnswer);
        $timeTaken = time() - $this->startTime;

        $attempt = ExerciseAttempt::create([
            'exercise_id' => $this->exercise->id,
            'user_id' => auth()->id(),
            'user_answers' => $userAnswer,
            'is_correct' => $isCorrect,
            'score' => $isCorrect ? $this->exercise->points : 0,
            'max_score' => $this->exercise->points,
            'accuracy_percentage' => $isCorrect ? 100 : 0,
            'time_taken' => $timeTaken,
            'started_at' => now()->subSeconds($timeTaken),
            'completed_at' => now(),
        ]);

        if ($isCorrect) {
            Notification::make()
                ->title('Correct!')
                ->success()
                ->body("You earned {$this->exercise->points} points! " .
                       ($this->exercise->explanation ? "Explanation: " . strip_tags($this->exercise->explanation) : ''))
                ->duration(10000)
                ->send();
        } else {
            $correctAnswers = implode(', ', $this->exercise->correct_answers);
            Notification::make()
                ->title('Incorrect')
                ->danger()
                ->body("The correct answer was: {$correctAnswers}. " .
                       ($this->exercise->explanation ? "Explanation: " . strip_tags($this->exercise->explanation) : ''))
                ->duration(10000)
                ->send();
        }

        // Load next exercise
        $this->mount();
    }

    public function skip(): void
    {
        Notification::make()
            ->title('Exercise skipped')
            ->info()
            ->send();

        $this->mount();
    }

    protected function getFormActions(): array
    {
        return [
            Forms\Components\Actions\Action::make('submit')
                ->label('Submit Answer')
                ->action('submit')
                ->color('success')
                ->icon('heroicon-o-check-circle')
                ->size('lg'),
            Forms\Components\Actions\Action::make('skip')
                ->label('Skip')
                ->action('skip')
                ->color('gray')
                ->icon('heroicon-o-forward')
                ->size('lg'),
        ];
    }
}
