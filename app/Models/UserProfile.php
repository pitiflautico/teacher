<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'avatar',
        'bio',
        'location',
        'website',
        'twitter',
        'linkedin',
        'study_schedule',
        'daily_goal_minutes',
        'preferred_ai_provider',
        'ai_creativity',
        'ai_tone',
        'profile_public',
        'show_progress',
        'show_badges',
    ];

    protected $casts = [
        'profile_public' => 'boolean',
        'show_progress' => 'boolean',
        'show_badges' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
