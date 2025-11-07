<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserProfileResource\Pages;
use App\Models\UserProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class UserProfileResource extends Resource
{
    protected static ?string $model = UserProfile::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationGroup = 'Social';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Profile')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->directory('avatars')
                            ->imageEditor(),
                        Forms\Components\Textarea::make('bio')
                            ->rows(4)
                            ->maxLength(500),
                    ]),

                Forms\Components\Section::make('Contact & Social')
                    ->schema([
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('twitter')
                            ->prefix('@')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('linkedin')
                            ->url()
                            ->maxLength(255),
                    ])->columns(2),

                Forms\Components\Section::make('Study Preferences')
                    ->schema([
                        Forms\Components\Select::make('study_schedule')
                            ->options([
                                'morning' => 'Morning',
                                'afternoon' => 'Afternoon',
                                'evening' => 'Evening',
                                'night' => 'Night',
                            ])
                            ->default('afternoon'),
                        Forms\Components\TextInput::make('daily_goal_minutes')
                            ->numeric()
                            ->default(120)
                            ->suffix('minutes'),
                    ])->columns(2),

                Forms\Components\Section::make('AI Preferences')
                    ->schema([
                        Forms\Components\Select::make('preferred_ai_provider')
                            ->options([
                                'openai' => 'OpenAI (GPT-4o-mini)',
                                'replicate' => 'Replicate (Llama 2)',
                                'together' => 'Together.ai (Llama 3.1)',
                            ])
                            ->default('openai'),
                        Forms\Components\Select::make('ai_tone')
                            ->options([
                                'formal' => 'Formal',
                                'casual' => 'Casual',
                                'friendly' => 'Friendly',
                                'professional' => 'Professional',
                            ])
                            ->default('friendly'),
                        Forms\Components\TextInput::make('ai_creativity')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10)
                            ->default(7)
                            ->helperText('0 = Conservative, 10 = Very Creative'),
                    ])->columns(3),

                Forms\Components\Section::make('Privacy')
                    ->schema([
                        Forms\Components\Toggle::make('profile_public')
                            ->default(true),
                        Forms\Components\Toggle::make('show_progress')
                            ->default(true),
                        Forms\Components\Toggle::make('show_badges')
                            ->default(true),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular(),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location'),
                Tables\Columns\TextColumn::make('preferred_ai_provider')
                    ->label('AI Provider')
                    ->badge(),
                Tables\Columns\IconColumn::make('profile_public')
                    ->boolean(),
                Tables\Columns\TextColumn::make('daily_goal_minutes')
                    ->suffix(' min')
                    ->alignCenter(),
            ])
            ->filters([
                Tables\Filters\Filter::make('public')
                    ->query(fn ($query) => $query->where('profile_public', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserProfiles::route('/'),
            'create' => Pages\CreateUserProfile::route('/create'),
            'edit' => Pages\EditUserProfile::route('/{record}/edit'),
        ];
    }
}
