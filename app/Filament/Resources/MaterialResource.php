<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaterialResource\Pages;
use App\Filament\Resources\MaterialResource\RelationManagers;
use App\Jobs\GenerateExercises;
use App\Jobs\GenerateFlashcardsFromMaterial;
use App\Jobs\GenerateNotesFromMaterial;
use App\Jobs\GenerateSummaryFromMaterial;
use App\Jobs\ProcessMaterialWithOCR;
use App\Models\Material;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class MaterialResource extends Resource
{
    protected static ?string $model = Material::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->relationship('user', 'name')
                    ->required(),
                Forms\Components\Select::make('subject_id')
                    ->relationship('subject', 'name')
                    ->default(null),
                Forms\Components\Select::make('topic_id')
                    ->relationship('topic', 'name')
                    ->default(null),
                Forms\Components\TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('type')
                    ->required(),
                Forms\Components\TextInput::make('file_path')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('original_filename')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('mime_type')
                    ->maxLength(255)
                    ->default(null),
                Forms\Components\TextInput::make('file_size')
                    ->numeric()
                    ->default(null),
                Forms\Components\Textarea::make('extracted_text')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('ai_metadata')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_processed')
                    ->required(),
                Forms\Components\DateTimePicker::make('processed_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('topic.name')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type'),
                Tables\Columns\TextColumn::make('file_path')
                    ->searchable(),
                Tables\Columns\TextColumn::make('original_filename')
                    ->searchable(),
                Tables\Columns\TextColumn::make('mime_type')
                    ->searchable(),
                Tables\Columns\TextColumn::make('file_size')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_processed')
                    ->boolean(),
                Tables\Columns\TextColumn::make('processed_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('processOCR')
                    ->label('Process with OCR')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->visible(fn (Material $record): bool =>
                        !$record->is_processed &&
                        $record->file_path &&
                        in_array($record->mime_type, ['image/jpeg', 'image/png', 'image/jpg', 'application/pdf'])
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Process Material with OCR')
                    ->modalDescription(fn (Material $record) => "Extract text from '{$record->title}' using OCR?")
                    ->modalIcon('heroicon-o-document-text')
                    ->action(function (Material $record) {
                        ProcessMaterialWithOCR::dispatch($record);

                        Notification::make()
                            ->title('OCR Processing Started')
                            ->success()
                            ->body('The material is being processed in the background.')
                            ->send();
                    }),
                Tables\Actions\Action::make('generateExercises')
                    ->label('Generate Exercises')
                    ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->visible(fn (Material $record): bool => $record->is_processed)
                    ->form([
                        Forms\Components\Select::make('type')
                            ->label('Exercise Type')
                            ->options([
                                'multiple_choice' => 'Multiple Choice',
                                'true_false' => 'True/False',
                                'short_answer' => 'Short Answer',
                                'essay' => 'Essay',
                                'problem_solving' => 'Problem Solving',
                            ])
                            ->required()
                            ->default('multiple_choice'),
                        Forms\Components\Select::make('difficulty')
                            ->options([
                                'easy' => 'Easy',
                                'medium' => 'Medium',
                                'hard' => 'Hard',
                            ])
                            ->required()
                            ->default('medium'),
                        Forms\Components\TextInput::make('count')
                            ->label('Number of Exercises')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(20)
                            ->default(5)
                            ->required(),
                    ])
                    ->action(function (Material $record, array $data) {
                        GenerateExercises::dispatch(
                            material: $record,
                            type: $data['type'],
                            difficulty: $data['difficulty'],
                            count: $data['count']
                        );

                        Notification::make()
                            ->title('Exercise Generation Started')
                            ->success()
                            ->body("Generating {$data['count']} {$data['difficulty']} exercises...")
                            ->send();
                    }),
                Tables\Actions\Action::make('generateSummary')
                    ->label('Generate Summary')
                    ->icon('heroicon-o-document-text')
                    ->color('warning')
                    ->visible(fn (Material $record): bool =>
                        $record->is_processed &&
                        !empty($record->extracted_text)
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Generate Summary with AI')
                    ->modalDescription(fn (Material $record) => "Generate a comprehensive summary from '{$record->title}'?")
                    ->modalIcon('heroicon-o-sparkles')
                    ->action(function (Material $record) {
                        GenerateSummaryFromMaterial::dispatch($record);

                        Notification::make()
                            ->title('Summary Generation Started')
                            ->success()
                            ->body('Generating summary with AI...')
                            ->send();
                    }),
                Tables\Actions\Action::make('generateNotes')
                    ->label('Generate Notes')
                    ->icon('heroicon-o-pencil-square')
                    ->color('info')
                    ->visible(fn (Material $record): bool =>
                        $record->is_processed &&
                        !empty($record->extracted_text)
                    )
                    ->requiresConfirmation()
                    ->modalHeading('Generate Study Notes with AI')
                    ->modalDescription(fn (Material $record) => "Generate structured study notes from '{$record->title}'?")
                    ->modalIcon('heroicon-o-sparkles')
                    ->action(function (Material $record) {
                        GenerateNotesFromMaterial::dispatch($record);

                        Notification::make()
                            ->title('Notes Generation Started')
                            ->success()
                            ->body('Generating study notes with AI...')
                            ->send();
                    }),
                Tables\Actions\Action::make('generateFlashcards')
                    ->label('Generate Flashcards')
                    ->icon('heroicon-o-light-bulb')
                    ->color('purple')
                    ->visible(fn (Material $record): bool =>
                        $record->is_processed &&
                        !empty($record->extracted_text)
                    )
                    ->form([
                        Forms\Components\TextInput::make('count')
                            ->label('Number of Flashcards')
                            ->numeric()
                            ->minValue(5)
                            ->maxValue(30)
                            ->default(10)
                            ->required()
                            ->helperText('How many flashcards to generate from this material'),
                    ])
                    ->action(function (Material $record, array $data) {
                        GenerateFlashcardsFromMaterial::dispatch($record, $data['count']);

                        Notification::make()
                            ->title('Flashcard Generation Started')
                            ->success()
                            ->body("Generating {$data['count']} flashcards with AI...")
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('bulkProcessOCR')
                        ->label('Process with OCR')
                        ->icon('heroicon-o-document-text')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if (!$record->is_processed && $record->file_path) {
                                    ProcessMaterialWithOCR::dispatch($record);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->title('Bulk OCR Processing Started')
                                ->success()
                                ->body("Processing {$count} materials in the background.")
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
                ]),
            ]);
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
            'index' => Pages\ListMaterials::route('/'),
            'create' => Pages\CreateMaterial::route('/create'),
            'edit' => Pages\EditMaterial::route('/{record}/edit'),
        ];
    }
}
