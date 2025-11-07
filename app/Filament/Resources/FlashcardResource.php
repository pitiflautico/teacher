<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FlashcardResource\Pages;
use App\Models\Flashcard;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FlashcardResource extends Resource
{
    protected static ?string $model = Flashcard::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationLabel = 'My Flashcards';

    protected static ?string $navigationGroup = 'Learning';

    protected static ?int $navigationSort = 3;

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Students only see their own flashcards
        if (auth()->user()->hasRole('estudiante')) {
            return $query->where('user_id', auth()->id());
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Card Content')
                    ->schema([
                        Forms\Components\TextInput::make('front')
                            ->label('Question / Term')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('e.g., What is the capital of France?'),
                        Forms\Components\Textarea::make('back')
                            ->label('Answer / Definition')
                            ->required()
                            ->rows(3)
                            ->placeholder('e.g., Paris'),
                    ]),

                Forms\Components\Section::make('Context')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->reactive(),
                        Forms\Components\Select::make('topic_id')
                            ->relationship('topic', 'name', fn($query, callable $get) =>
                                $get('subject_id') ? $query->where('subject_id', $get('subject_id')) : $query
                            )
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Additional Information')
                    ->schema([
                        Forms\Components\Textarea::make('hint')
                            ->rows(2)
                            ->placeholder('Optional hint to help remember'),
                        Forms\Components\Textarea::make('notes')
                            ->rows(2)
                            ->placeholder('Personal notes'),
                    ]),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('front')
                    ->label('Question')
                    ->searchable()
                    ->limit(50)
                    ->wrap(),
                Tables\Columns\TextColumn::make('back')
                    ->label('Answer')
                    ->searchable()
                    ->limit(50)
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('topic.name')
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('next_review_at')
                    ->label('Next Review')
                    ->dateTime('M d, H:i')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => $state && $state->isPast() ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('accuracy')
                    ->label('Accuracy')
                    ->suffix('%')
                    ->sortable()
                    ->alignCenter()
                    ->color(fn ($state) => match(true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        default => 'danger',
                    }),
                Tables\Columns\TextColumn::make('streak')
                    ->label('Streak')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('total_reviews')
                    ->label('Reviews')
                    ->alignCenter()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject')
                    ->relationship('subject', 'name'),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
                Tables\Filters\Filter::make('due')
                    ->label('Due for Review')
                    ->query(fn (Builder $query): Builder => $query->where('next_review_at', '<=', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('reset')
                    ->label('Reset')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Flashcard $record) => $record->reset()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('next_review_at');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFlashcards::route('/'),
            'create' => Pages\CreateFlashcard::route('/create'),
            'edit' => Pages\EditFlashcard::route('/{record}/edit'),
        ];
    }
}
