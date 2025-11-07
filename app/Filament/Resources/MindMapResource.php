<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MindMapResource\Pages;
use App\Models\MindMap;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MindMapResource extends Resource
{
    protected static ?string $model = MindMap::class;
    protected static ?string $navigationIcon = 'heroicon-o-share';
    protected static ?string $navigationGroup = 'Learning';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Mind Map Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                    ]),

                Forms\Components\Section::make('Related To')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('topic_id')
                            ->relationship('topic', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('material_id')
                            ->relationship('material', 'title')
                            ->searchable()
                            ->preload(),
                    ])->columns(3),

                Forms\Components\Section::make('Map Data')
                    ->schema([
                        Forms\Components\Textarea::make('nodes_data')
                            ->label('Nodes JSON')
                            ->rows(10)
                            ->helperText('JSON array of nodes: [{id, label, x, y, color}]'),
                        Forms\Components\Textarea::make('edges_data')
                            ->label('Edges JSON')
                            ->rows(5)
                            ->helperText('JSON array of edges: [{from, to, label}]'),
                    ]),

                Forms\Components\Section::make('Settings')
                    ->schema([
                        Forms\Components\Toggle::make('is_public')
                            ->default(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_public')
                    ->boolean(),
                Tables\Columns\TextColumn::make('views_count')
                    ->sortable()
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('subject')
                    ->relationship('subject', 'name'),
                Tables\Filters\Filter::make('public')
                    ->query(fn ($query) => $query->where('is_public', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('generate_flashcards')
                    ->icon('heroicon-o-sparkles')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (MindMap $record) => $record->generateFlashcards()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMindMaps::route('/'),
            'create' => Pages\CreateMindMap::route('/create'),
            'edit' => Pages\EditMindMap::route('/{record}/edit'),
        ];
    }
}
