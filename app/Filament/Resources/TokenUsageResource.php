<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TokenUsageResource\Pages;
use App\Filament\Resources\TokenUsageResource\RelationManagers;
use App\Models\TokenUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TokenUsageResource extends Resource
{
    protected static ?string $model = TokenUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?string $navigationGroup = 'AI Services';

    protected static ?int $navigationSort = 10;

    protected static ?string $navigationLabel = 'Token Usage';

    protected static ?string $pluralModelLabel = 'Token Usage Records';

    public static function canCreate(): bool
    {
        return false; // Read-only resource
    }

    public static function canEdit($record): bool
    {
        return false; // Read-only resource
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->hasRole('admin') ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Usage Details')
                    ->schema([
                        Forms\Components\TextInput::make('provider')
                            ->disabled(),
                        Forms\Components\TextInput::make('model')
                            ->disabled(),
                        Forms\Components\TextInput::make('type')
                            ->disabled(),
                        Forms\Components\TextInput::make('cost')
                            ->disabled()
                            ->prefix('$'),
                    ])->columns(4),

                Forms\Components\Section::make('Token Breakdown')
                    ->schema([
                        Forms\Components\TextInput::make('prompt_tokens')
                            ->disabled(),
                        Forms\Components\TextInput::make('completion_tokens')
                            ->disabled(),
                        Forms\Components\TextInput::make('total_tokens')
                            ->disabled(),
                    ])->columns(3),

                Forms\Components\Section::make('Content Preview')
                    ->schema([
                        Forms\Components\Textarea::make('input_preview')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('output_preview')
                            ->disabled()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BadgeColumn::make('provider')
                    ->colors([
                        'primary' => 'openai',
                        'success' => 'replicate',
                        'warning' => 'together',
                    ])
                    ->searchable(),
                Tables\Columns\TextColumn::make('model')
                    ->searchable()
                    ->wrap()
                    ->limit(30),
                Tables\Columns\BadgeColumn::make('type')
                    ->colors([
                        'info' => 'completion',
                        'success' => 'chat',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                Tables\Columns\TextColumn::make('prompt_tokens')
                    ->label('Input')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total'),
                    ]),
                Tables\Columns\TextColumn::make('completion_tokens')
                    ->label('Output')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total'),
                    ]),
                Tables\Columns\TextColumn::make('total_tokens')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total'),
                    ]),
                Tables\Columns\TextColumn::make('cost')
                    ->money('USD', divideBy: 1)
                    ->sortable()
                    ->alignEnd()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->label('Total')
                            ->money('USD', divideBy: 1),
                    ]),
                Tables\Columns\TextColumn::make('user.name')
                    ->label('User')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('input_preview')
                    ->label('Input')
                    ->limit(50)
                    ->wrap()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->options([
                        'openai' => 'OpenAI',
                        'replicate' => 'Replicate',
                        'together' => 'Together.ai',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'completion' => 'Completion',
                        'chat' => 'Chat',
                    ])
                    ->multiple(),
                Tables\Filters\SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->visible(fn() => auth()->user()?->hasRole('admin') ?? false),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
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
            'index' => Pages\ListTokenUsages::route('/'),
            'view' => Pages\ViewTokenUsage::route('/{record}'),
        ];
    }
}
