<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SavedResourceResource\Pages;
use App\Models\SavedResource;
use App\Models\Subject;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SavedResourceResource extends Resource
{
    protected static ?string $model = SavedResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-bookmark';

    protected static ?string $navigationLabel = 'My Saved Resources';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 21;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label(__('Title'))
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('url')
                            ->label(__('URL'))
                            ->url()
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\Select::make('type')
                            ->label(__('Type'))
                            ->options([
                                'pdf' => 'PDF',
                                'video' => __('Video'),
                                'exercise' => __('Exercise'),
                                'article' => __('Article'),
                            ])
                            ->required(),

                        Forms\Components\Select::make('subject_id')
                            ->label(__('Subject'))
                            ->options(Subject::where('user_id', auth()->id())->pluck('name', 'id'))
                            ->searchable()
                            ->preload(),

                        Forms\Components\Textarea::make('snippet')
                            ->label(__('Description'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Textarea::make('notes')
                            ->label(__('Personal Notes'))
                            ->helperText(__('Your private notes about this resource'))
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\Toggle::make('is_favorite')
                            ->label(__('Favorite'))
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\IconColumn::make('type')
                    ->label(__('Type'))
                    ->icon(fn (string $state): string => match ($state) {
                        'pdf' => 'heroicon-o-document-text',
                        'video' => 'heroicon-o-play-circle',
                        'exercise' => 'heroicon-o-pencil-square',
                        default => 'heroicon-o-document',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pdf' => 'danger',
                        'video' => 'info',
                        'exercise' => 'success',
                        default => 'gray',
                    })
                    ->size('lg'),

                Tables\Columns\TextColumn::make('title')
                    ->label(__('Title'))
                    ->searchable()
                    ->limit(50)
                    ->weight('bold')
                    ->url(fn (SavedResource $record) => $record->url, true),

                Tables\Columns\TextColumn::make('subject.name')
                    ->label(__('Subject'))
                    ->badge()
                    ->color('primary')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_favorite')
                    ->label(__('Favorite'))
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning'),

                Tables\Columns\TextColumn::make('relevance')
                    ->label(__('Quality'))
                    ->suffix('%')
                    ->color(fn (int $state): string => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('accessed_at')
                    ->label(__('Last Accessed'))
                    ->dateTime()
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Saved At'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label(__('Type'))
                    ->options([
                        'pdf' => 'PDF',
                        'video' => __('Video'),
                        'exercise' => __('Exercise'),
                        'article' => __('Article'),
                    ]),

                Tables\Filters\SelectFilter::make('subject_id')
                    ->label(__('Subject'))
                    ->relationship('subject', 'name'),

                Tables\Filters\TernaryFilter::make('is_favorite')
                    ->label(__('Favorites'))
                    ->trueLabel(__('Favorites only'))
                    ->falseLabel(__('Not favorites'))
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('open')
                    ->label(__('Open'))
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->url(fn (SavedResource $record) => $record->url, true)
                    ->after(fn (SavedResource $record) => $record->markAsAccessed()),

                Tables\Actions\Action::make('toggle_favorite')
                    ->label(fn (SavedResource $record) => $record->is_favorite ? __('Unfavorite') : __('Favorite'))
                    ->icon(fn (SavedResource $record) => $record->is_favorite ? 'heroicon-s-star' : 'heroicon-o-star')
                    ->color('warning')
                    ->action(fn (SavedResource $record) => $record->toggleFavorite()),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSavedResources::route('/'),
            'edit' => Pages\EditSavedResource::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('user_id', auth()->id())->count();
    }
}
