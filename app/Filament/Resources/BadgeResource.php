<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BadgeResource\Pages;
use App\Models\Badge;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BadgeResource extends Resource
{
    protected static ?string $model = Badge::class;
    protected static ?string $navigationIcon = 'heroicon-o-trophy';
    protected static ?string $navigationGroup = 'Gamification';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Badge Details')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Unique identifier for this badge'),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\TextInput::make('icon')
                            ->maxLength(255)
                            ->helperText('Icon name or emoji'),
                    ]),

                Forms\Components\Section::make('Requirements')
                    ->schema([
                        Forms\Components\Select::make('requirement_type')
                            ->options([
                                'exercises_completed' => 'Exercises Completed',
                                'materials_studied' => 'Materials Studied',
                                'flashcards_reviewed' => 'Flashcards Reviewed',
                                'points_earned' => 'Points Earned',
                                'streak_days' => 'Streak Days',
                                'groups_joined' => 'Groups Joined',
                                'mind_maps_created' => 'Mind Maps Created',
                            ])
                            ->helperText('What action needs to be completed'),
                        Forms\Components\TextInput::make('requirement_value')
                            ->numeric()
                            ->helperText('How many times the action must be completed'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icon')
                    ->label('Icon')
                    ->alignCenter()
                    ->size('lg'),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('requirement_type')
                    ->label('Requirement')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title()),
                Tables\Columns\TextColumn::make('requirement_value')
                    ->label('Value')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('users_count')
                    ->label('Unlocked By')
                    ->counts('users')
                    ->alignCenter()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('requirement_type')
                    ->options([
                        'exercises_completed' => 'Exercises Completed',
                        'materials_studied' => 'Materials Studied',
                        'flashcards_reviewed' => 'Flashcards Reviewed',
                        'points_earned' => 'Points Earned',
                        'streak_days' => 'Streak Days',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListBadges::route('/'),
            'create' => Pages\CreateBadge::route('/create'),
            'edit' => Pages\EditBadge::route('/{record}/edit'),
        ];
    }
}
