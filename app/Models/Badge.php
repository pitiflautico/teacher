<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Badge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'requirement_type',
        'requirement_value',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_badges')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function userBadges()
    {
        return $this->hasMany(UserBadge::class);
    }

    /**
     * Check if user has unlocked this badge
     */
    public function isUnlockedBy(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }

    /**
     * Award this badge to a user
     */
    public function awardTo(User $user)
    {
        if (!$this->isUnlockedBy($user)) {
            $this->users()->attach($user->id, [
                'unlocked_at' => now(),
            ]);

            // Award points for unlocking badge
            Point::create([
                'user_id' => $user->id,
                'pointable_type' => Badge::class,
                'pointable_id' => $this->id,
                'points' => 100, // Badge unlock = 100 points
                'reason' => "Unlocked badge: {$this->name}",
            ]);
        }
    }
}
