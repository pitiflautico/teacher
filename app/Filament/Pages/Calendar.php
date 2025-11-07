<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Calendar extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Planning';
    protected static ?int $navigationSort = 2;
    protected static string $view = 'filament.pages.calendar';
    protected static ?string $title = 'My Calendar';

    public function getViewData(): array
    {
        $user = auth()->user();
        
        $events = \App\Models\CalendarEvent::where('user_id', $user->id)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'start' => $event->start_at->toIso8601String(),
                    'end' => $event->end_at?->toIso8601String(),
                    'allDay' => $event->all_day,
                    'backgroundColor' => $event->color ?? '#3b82f6',
                    'borderColor' => $event->color ?? '#3b82f6',
                    'extendedProps' => [
                        'description' => $event->description,
                        'type' => $event->type,
                        'location' => $event->location,
                    ],
                ];
            });

        return [
            'events' => $events->toJson(),
        ];
    }
}
