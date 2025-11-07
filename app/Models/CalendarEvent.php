<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CalendarEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_id',
        'title',
        'description',
        'type',
        'start_at',
        'end_at',
        'all_day',
        'location',
        'color',
        'google_event_id',
        'synced_at',
        'reminder_enabled',
        'reminder_minutes',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'synced_at' => 'datetime',
        'all_day' => 'boolean',
        'reminder_enabled' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>=', now())
            ->orderBy('start_at', 'asc');
    }

    public function scopePast($query)
    {
        return $query->where('start_at', '<', now())
            ->orderBy('start_at', 'desc');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_at', today());
    }
}
