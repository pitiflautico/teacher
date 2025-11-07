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
                    ->description('Choose your preferred AI provider for content generation. Each provider has different models, pricing, and capabilities.')
                    ->schema([
                        Forms\Components\Select::make('preferred_ai_provider')
                            ->label('Preferred AI Provider')
                            ->options([
                                'openai' => 'OpenAI (GPT-4o-mini) - Fastest & Most Accurate',
                                'together' => 'Together.ai (Llama 3.1) - Cost-Effective & Fast',
                                'replicate' => 'Replicate (Llama 2) - Open Source & Flexible',
                            ])
                            ->default('openai')
                            ->helperText('Your selected provider will be used for exercise generation, flashcard creation, and AI explanations.')
                            ->columnSpanFull(),
                        Forms\Components\Placeholder::make('provider_info')
                            ->label('Provider Information')
                            ->content(function () {
                                $info = "**Available Providers:**\n\n";
                                $info .= "• **OpenAI**: Best quality, $0.15-$0.60 per 1M tokens\n";
                                $info .= "• **Together.ai**: Fast & affordable, $0.18-$0.88 per 1M tokens\n";
                                $info .= "• **Replicate**: Pay per second, $0.65-$2.75 per 1M tokens\n\n";
                                $info .= "You can change providers anytime to compare results.";
                                return new \Illuminate\Support\HtmlString(
                                    '<div class="text-sm text-gray-600 dark:text-gray-400">' .
                                    nl2br($info) .
                                    '</div>'
                                );
                            })
                            ->columnSpanFull(),
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
