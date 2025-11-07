<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CalendarEventResource\Pages;
use App\Models\CalendarEvent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CalendarEventResource extends Resource
{
    protected static ?string $model = CalendarEvent::class;
    protected static ?string $navigationIcon = 'heroicon-o-calendar';
    protected static ?string $navigationGroup = 'Planning';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Event Details')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->rows(3),
                        Forms\Components\Select::make('type')
                            ->options([
                                'class' => 'Class',
                                'exam' => 'Exam',
                                'task' => 'Task',
                                'study' => 'Study Session',
                                'other' => 'Other',
                            ])
                            ->required()
                            ->default('other'),
                    ]),

                Forms\Components\Section::make('Schedule')
                    ->schema([
                        Forms\Components\DateTimePicker::make('start_at')
                            ->required()
                            ->seconds(false),
                        Forms\Components\DateTimePicker::make('end_at')
                            ->seconds(false)
                            ->after('start_at'),
                        Forms\Components\Toggle::make('all_day')
                            ->default(false),
                    ])->columns(3),

                Forms\Components\Section::make('Additional Info')
                    ->schema([
                        Forms\Components\Select::make('subject_id')
                            ->relationship('subject', 'name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('location')
                            ->maxLength(255),
                        Forms\Components\ColorPicker::make('color'),
                    ])->columns(3),

                Forms\Components\Section::make('Reminders')
                    ->schema([
                        Forms\Components\Toggle::make('reminder_enabled')
                            ->default(true),
                        Forms\Components\TextInput::make('reminder_minutes')
                            ->numeric()
                            ->default(30)
                            ->suffix('minutes before'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'class' => 'info',
                        'exam' => 'danger',
                        'task' => 'warning',
                        'study' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('subject.name')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_at')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_at')
                    ->dateTime('H:i')
                    ->sortable(),
                Tables\Columns\IconColumn::make('all_day')
                    ->boolean(),
                Tables\Columns\IconColumn::make('reminder_enabled')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'class' => 'Class',
                        'exam' => 'Exam',
                        'task' => 'Task',
                        'study' => 'Study',
                        'other' => 'Other',
                    ]),
                Tables\Filters\SelectFilter::make('subject')
                    ->relationship('subject', 'name'),
                Tables\Filters\Filter::make('upcoming')
                    ->query(fn ($query) => $query->where('start_at', '>=', now())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('start_at', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCalendarEvents::route('/'),
            'create' => Pages\CreateCalendarEvent::route('/create'),
            'edit' => Pages\EditCalendarEvent::route('/{record}/edit'),
        ];
    }
}
