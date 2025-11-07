<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Point extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pointable_type',
        'pointable_id',
        'points',
        'reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the parent pointable model (Exercise, Material, etc.)
     */
    public function pointable()
    {
        return $this->morphTo();
    }

    /**
     * Award points to a user for completing an action
     */
    public static function award(User $user, $pointable, int $points, string $reason)
    {
        return static::create([
            'user_id' => $user->id,
            'pointable_type' => get_class($pointable),
            'pointable_id' => $pointable->id,
            'points' => $points,
            'reason' => $reason,
        ]);
    }
}
