<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExerciseResource\Pages;
use App\Filament\Resources\ExerciseResource\RelationManagers;
use App\Models\Exercise;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ExerciseResource extends Resource
{
    protected static ?string $model = Exercise::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Content Management';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('General Information')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->required()
                            ->default(auth()->id())
                            ->hidden(fn() => !auth()->user()?->hasRole('admin')),
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive()
                            ->afterStateUpdated(fn($state, callable $set) => $set('topic_id', null)),
                        Forms\Components\Select::make('topic_id')
                            ->relationship('topic', 'name', fn($query, callable $get) =>
                                $get('subject_id') ? $query->where('subject_id', $get('subject_id')) : $query
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),
                        Forms\Components\Select::make('material_id')
                            ->relationship('material', 'title')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Link this exercise to specific study material'),
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., Quadratic Equations Practice'),
                    ])->columns(2),

                Forms\Components\Section::make('Exercise Configuration')
                    ->schema([
                        Forms\Components\Select::make('type')
                            ->options([
                                'multiple_choice' => 'Multiple Choice',
                                'true_false' => 'True/False',
                                'short_answer' => 'Short Answer',
                                'essay' => 'Essay',
                                'problem_solving' => 'Problem Solving',
                            ])
                            ->required()
                            ->default('multiple_choice')
                            ->reactive(),
                        Forms\Components\Select::make('difficulty')
                            ->options([
                                'easy' => 'Easy',
                                'medium' => 'Medium',
                                'hard' => 'Hard',
                            ])
                            ->required()
                            ->default('medium'),
                        Forms\Components\TextInput::make('points')
                            ->required()
                            ->numeric()
                            ->default(10)
                            ->minValue(1)
                            ->maxValue(100),
                        Forms\Components\TextInput::make('time_limit')
                            ->numeric()
                            ->nullable()
                            ->suffix('seconds')
                            ->helperText('Leave empty for no time limit'),
                    ])->columns(4),

                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->rows(2)
                            ->placeholder('Brief description of the exercise')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('question')
                            ->required()
                            ->placeholder('Enter the exercise question')
                            ->columnSpanFull(),
                        Forms\Components\KeyValue::make('options')
                            ->keyLabel('Option')
                            ->valueLabel('Text')
                            ->addActionLabel('Add option')
                            ->visible(fn(callable $get) => in_array($get('type'), ['multiple_choice']))
                            ->columnSpanFull(),
                        Forms\Components\TagsInput::make('correct_answers')
                            ->required()
                            ->placeholder('Enter correct answer(s)')
                            ->helperText('For multiple choice, enter option keys (e.g., A, B). For other types, enter the correct answer.')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('explanation')
                            ->placeholder('Explanation of the correct answer')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('hints')
                            ->rows(2)
                            ->placeholder('Helpful hints (one per line)')
                            ->columnSpanFull(),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('contains_math')
                            ->label('Contains Mathematical Formulas')
                            ->helperText('Enable KaTeX rendering for mathematical expressions')
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->helperText('Only active exercises are visible to students')
                            ->default(true),
                        Forms\Components\KeyValue::make('ai_metadata')
                            ->label('AI Metadata')
                            ->keyLabel('Key')
                            ->valueLabel('Value')
                            ->columnSpanFull()
                            ->hidden(fn() => !auth()->user()?->hasRole('admin')),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->wrap(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('topic.name')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'primary' => 'multiple_choice',
                        'success' => 'true_false',
                        'warning' => 'short_answer',
                        'danger' => 'essay',
                        'info' => 'problem_solving',
                    ])
                    ->formatStateUsing(fn (string $state): string => str_replace('_', ' ', ucwords($state, '_'))),
                Tables\Columns\BadgeColumn::make('difficulty')
                    ->colors([
                        'success' => 'easy',
                        'warning' => 'medium',
                        'danger' => 'hard',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('points')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('time_limit')
                    ->numeric()
                    ->sortable()
                    ->suffix(' sec')
                    ->alignCenter()
                    ->placeholder('No limit')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('contains_math')
                    ->boolean()
                    ->label('Math')
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Created by')
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('attempts_count')
                    ->counts('attempts')
                    ->label('Attempts')
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'multiple_choice' => 'Multiple Choice',
                        'true_false' => 'True/False',
                        'short_answer' => 'Short Answer',
                        'essay' => 'Essay',
                        'problem_solving' => 'Problem Solving',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('difficulty')
                    ->options([
                        'easy' => 'Easy',
                        'medium' => 'Medium',
                        'hard' => 'Hard',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('subject')
                    ->relationship('subject', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\SelectFilter::make('topic')
                    ->relationship('topic', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\TernaryFilter::make('contains_math')
                    ->label('Contains Math'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['is_active' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->action(fn ($records) => $records->each->update(['is_active' => false]))
                        ->deselectRecordsAfterCompletion(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListExercises::route('/'),
            'create' => Pages\CreateExercise::route('/create'),
            'edit' => Pages\EditExercise::route('/{record}/edit'),
        ];
    }
}
