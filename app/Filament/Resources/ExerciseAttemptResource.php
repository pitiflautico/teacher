<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExerciseAttemptResource\Pages;
use App\Filament\Resources\ExerciseAttemptResource\RelationManagers;
use App\Models\ExerciseAttempt;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExerciseAttemptResource extends Resource
{
    protected static ?string $model = ExerciseAttempt::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'My Progress';

    protected static ?string $modelLabel = 'Exercise Attempt';

    protected static ?string $pluralModelLabel = 'Exercise Attempts';

    protected static ?string $navigationGroup = 'Learning';

    protected static ?int $navigationSort = 2;

    public static function canCreate(): bool
    {
        return false; // Attempts are created through TakeExercise page
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Students only see their own attempts
        if (auth()->user()->hasRole('estudiante')) {
            return $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('exercise_id')
                    ->relationship('exercise', 'title')
                    ->required(),
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Textarea::make('user_answers')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_correct'),
                Forms\Components\TextInput::make('score')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('max_score')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('accuracy_percentage')
                    ->numeric()
                    ->default(null),
                Forms\Components\TextInput::make('time_taken')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('ai_feedback')
                    ->columnSpanFull(),
                Forms\Components\DateTimePicker::make('started_at'),
                Forms\Components\DateTimePicker::make('completed_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('exercise.title')
                    ->label('Exercise')
                    ->searchable()
                    ->sortable()
                    ->wrap()
                    ->limit(50),
                Tables\Columns\TextColumn::make('exercise.subject.name')
                    ->label('Subject')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('exercise.topic.name')
                    ->label('Topic')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('exercise.difficulty')
                    ->label('Difficulty')
                    ->colors([
                        'success' => 'easy',
                        'warning' => 'medium',
                        'danger' => 'hard',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\IconColumn::make('is_correct')
                    ->label('Result')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->formatStateUsing(fn ($record) => "{$record->score}/{$record->max_score}")
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('accuracy_percentage')
                    ->label('Accuracy')
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state) => match(true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('time_taken')
                    ->label('Time')
                    ->formatStateUsing(fn ($state) => gmdate('i:s', $state ?? 0))
                    ->sortable()
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->toggleable()
                    ->visible(fn () => auth()->user()->hasRole('admin') || auth()->user()->hasRole('profesor')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('is_correct')
                    ->label('Result')
                    ->options([
                        1 => 'Correct',
                        0 => 'Incorrect',
                    ]),
                Tables\Filters\SelectFilter::make('exercise.difficulty')
                    ->label('Difficulty')
                    ->relationship('exercise', 'difficulty')
                    ->options([
                        'easy' => 'Easy',
                        'medium' => 'Medium',
                        'hard' => 'Hard',
                    ]),
                Tables\Filters\SelectFilter::make('exercise.subject_id')
                    ->label('Subject')
                    ->relationship('exercise.subject', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('completed_at')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('completed_at', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('completed_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn () => auth()->user()->hasRole('admin')),
                ]),
            ])
            ->defaultSort('completed_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListExerciseAttempts::route('/'),
            'create' => Pages\CreateExerciseAttempt::route('/create'),
            'edit' => Pages\EditExerciseAttempt::route('/{record}/edit'),
        ];
    }
}
