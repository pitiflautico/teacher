<?php

namespace App\Filament\Pages;

use App\Models\UserAiProvider;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

class ManageAiProviders extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-cpu-chip';

    protected static string $view = 'filament.pages.manage-ai-providers';

    protected static ?string $navigationLabel = 'AI Providers';

    protected static ?string $title = 'AI Provider Settings';

    protected static ?string $navigationGroup = 'System';

    protected static ?int $navigationSort = 100;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                UserAiProvider::query()
                    ->where('user_id', auth()->id())
            )
            ->columns([
                Tables\Columns\TextColumn::make('provider_label')
                    ->label(__('Provider'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->label(__('Active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('tokens_used')
                    ->label(__('Tokens Used'))
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->token_limit
                            ? number_format($state) . ' / ' . number_format($record->token_limit)
                            : number_format($state)
                    )
                    ->badge()
                    ->color(fn ($record) =>
                        $record->hasReachedTokenLimit() ? 'danger' : 'success'
                    ),
                Tables\Columns\TextColumn::make('cost_spent')
                    ->label(__('Cost Spent'))
                    ->formatStateUsing(fn ($state, $record) =>
                        $record->cost_limit
                            ? '$' . number_format($state, 2) . ' / $' . number_format($record->cost_limit, 2)
                            : '$' . number_format($state, 2)
                    )
                    ->badge()
                    ->color(fn ($record) =>
                        $record->hasReachedCostLimit() ? 'danger' : 'success'
                    ),
                Tables\Columns\TextColumn::make('usage_reset_at')
                    ->label(__('Resets'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Add Provider'))
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading(__('Add AI Provider'))
                    ->form([
                        Forms\Components\Select::make('provider')
                            ->label(__('Provider'))
                            ->options([
                                'openai' => 'OpenAI (GPT-4, GPT-3.5)',
                                'anthropic' => 'Anthropic (Claude)',
                                'google' => 'Google (Gemini)',
                                'replicate' => 'Replicate (Llama, etc.)',
                                'together' => 'Together AI (Llama 3.1)',
                            ])
                            ->required()
                            ->unique('user_ai_providers', 'provider', modifyRuleUsing: function ($rule) {
                                return $rule->where('user_id', auth()->id());
                            })
                            ->helperText(__('Choose the AI provider you want to use')),
                        Forms\Components\TextInput::make('api_key')
                            ->label(__('API Key'))
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Your API key will be encrypted and stored securely')),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true)
                            ->helperText(__('Enable this provider for use')),
                        Forms\Components\Section::make(__('Usage Limits'))
                            ->description(__('Set optional limits to control your spending'))
                            ->schema([
                                Forms\Components\TextInput::make('token_limit')
                                    ->label(__('Monthly Token Limit'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText(__('Leave empty for unlimited. Resets monthly.')),
                                Forms\Components\TextInput::make('cost_limit')
                                    ->label(__('Monthly Cost Limit ($)'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText(__('Leave empty for unlimited. Resets monthly.')),
                            ])
                            ->columns(2),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        $data['usage_reset_at'] = now()->addMonth();
                        return $data;
                    })
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('Provider added'))
                            ->body(__('Your AI provider has been configured successfully.'))
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->modalHeading(__('Edit AI Provider'))
                    ->form([
                        Forms\Components\TextInput::make('provider')
                            ->label(__('Provider'))
                            ->disabled()
                            ->dehydrated(false),
                        Forms\Components\TextInput::make('api_key')
                            ->label(__('API Key'))
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Enter a new API key or leave as is')),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->helperText(__('Enable/disable this provider')),
                        Forms\Components\Section::make(__('Usage Limits'))
                            ->schema([
                                Forms\Components\TextInput::make('token_limit')
                                    ->label(__('Monthly Token Limit'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText(__('Leave empty for unlimited')),
                                Forms\Components\TextInput::make('cost_limit')
                                    ->label(__('Monthly Cost Limit ($)'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText(__('Leave empty for unlimited')),
                            ])
                            ->columns(2),
                    ])
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('Provider updated'))
                            ->body(__('Your AI provider settings have been updated.'))
                    ),
                Tables\Actions\Action::make('configure_services')
                    ->label(__('Configure Services'))
                    ->icon('heroicon-o-cog-6-tooth')
                    ->color('primary')
                    ->modalHeading(__('Configure Services'))
                    ->modalDescription(fn ($record) => __('Select which services you want to use with :provider', ['provider' => $record->provider_label]))
                    ->form(function ($record) {
                        $availableServices = \App\Models\ProviderService::getAvailableServices($record->provider);
                        $formFields = [];

                        foreach ($availableServices as $serviceType => $serviceInfo) {
                            $formFields[] = Forms\Components\Section::make($serviceInfo['label'])
                                ->description($serviceInfo['description'])
                                ->schema([
                                    Forms\Components\Toggle::make("services.{$serviceType}.enabled")
                                        ->label(__('Enable this service'))
                                        ->default(function () use ($record, $serviceType) {
                                            return $record->services()->where('service_type', $serviceType)->exists();
                                        })
                                        ->reactive(),
                                    Forms\Components\Select::make("services.{$serviceType}.model")
                                        ->label(__('Model'))
                                        ->options($serviceInfo['models'])
                                        ->default(function () use ($record, $serviceType, $serviceInfo) {
                                            $service = $record->services()->where('service_type', $serviceType)->first();
                                            return $service?->model ?? array_key_first($serviceInfo['models']);
                                        })
                                        ->visible(fn ($get) => $get("services.{$serviceType}.enabled"))
                                        ->required(),
                                ]);
                        }

                        return $formFields;
                    })
                    ->action(function (UserAiProvider $record, array $data) {
                        $availableServices = \App\Models\ProviderService::getAvailableServices($record->provider);

                        foreach ($availableServices as $serviceType => $serviceInfo) {
                            if (isset($data['services'][$serviceType]['enabled'])) {
                                if ($data['services'][$serviceType]['enabled']) {
                                    // Create or update service
                                    $record->services()->updateOrCreate(
                                        ['service_type' => $serviceType],
                                        [
                                            'model' => $data['services'][$serviceType]['model'],
                                            'is_active' => true,
                                        ]
                                    );
                                } else {
                                    // Disable service
                                    $record->services()->where('service_type', $serviceType)->delete();
                                }
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title(__('Services configured'))
                            ->body(__('Your services have been configured successfully.'))
                            ->send();
                    }),
                Tables\Actions\Action::make('reset_usage')
                    ->label(__('Reset Usage'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (UserAiProvider $record) {
                        $record->update([
                            'tokens_used' => 0,
                            'cost_spent' => 0,
                            'usage_reset_at' => now()->addMonth(),
                        ]);

                        Notification::make()
                            ->success()
                            ->title(__('Usage reset'))
                            ->body(__('Usage statistics have been reset.'))
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('Provider deleted'))
                            ->body(__('The AI provider has been removed.'))
                    ),
            ])
            ->emptyStateHeading(__('No AI providers configured'))
            ->emptyStateDescription(__('Add your first AI provider to start using AI features'))
            ->emptyStateIcon('heroicon-o-cpu-chip')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label(__('Add Your First Provider'))
                    ->icon('heroicon-o-plus-circle')
                    ->modalHeading(__('Add AI Provider'))
                    ->form([
                        Forms\Components\Select::make('provider')
                            ->label(__('Provider'))
                            ->options([
                                'openai' => 'OpenAI (GPT-4, GPT-3.5)',
                                'anthropic' => 'Anthropic (Claude)',
                                'google' => 'Google (Gemini)',
                                'replicate' => 'Replicate (Llama, etc.)',
                                'together' => 'Together AI (Llama 3.1)',
                            ])
                            ->required()
                            ->helperText(__('Choose the AI provider you want to use')),
                        Forms\Components\TextInput::make('api_key')
                            ->label(__('API Key'))
                            ->password()
                            ->required()
                            ->maxLength(255)
                            ->helperText(__('Your API key will be encrypted and stored securely')),
                        Forms\Components\Toggle::make('is_active')
                            ->label(__('Active'))
                            ->default(true),
                        Forms\Components\Section::make(__('Usage Limits'))
                            ->description(__('Set optional limits to control your spending'))
                            ->schema([
                                Forms\Components\TextInput::make('token_limit')
                                    ->label(__('Monthly Token Limit'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText(__('Leave empty for unlimited')),
                                Forms\Components\TextInput::make('cost_limit')
                                    ->label(__('Monthly Cost Limit ($)'))
                                    ->numeric()
                                    ->minValue(0)
                                    ->step(0.01)
                                    ->helperText(__('Leave empty for unlimited')),
                            ])
                            ->columns(2),
                    ])
                    ->mutateFormDataUsing(function (array $data): array {
                        $data['user_id'] = auth()->id();
                        $data['usage_reset_at'] = now()->addMonth();
                        return $data;
                    }),
            ]);
    }
}
