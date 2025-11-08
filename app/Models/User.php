<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function subjects(): HasMany
    {
        return $this->hasMany(Subject::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    // Calendar Events
    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    // Mind Maps
    public function mindMaps(): HasMany
    {
        return $this->hasMany(MindMap::class);
    }

    // User Profile
    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    // Groups (created by user)
    public function createdGroups(): HasMany
    {
        return $this->hasMany(Group::class);
    }

    // Groups (member of)
    public function groups()
    {
        return $this->belongsToMany(Group::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    // Messages sent
    public function sentMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    // Messages received
    public function receivedMessages(): HasMany
    {
        return $this->hasMany(Message::class, 'recipient_id');
    }

    // Following
    public function following()
    {
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id')
            ->withTimestamps();
    }

    // Followers
    public function followers()
    {
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id')
            ->withTimestamps();
    }

    // Helper methods
    public function isFollowing(User $user)
    {
        return $this->following()->where('following_id', $user->id)->exists();
    }

    public function follow(User $user)
    {
        if (!$this->isFollowing($user)) {
            $this->following()->attach($user->id);
        }
    }

    public function unfollow(User $user)
    {
        $this->following()->detach($user->id);
    }

    // Gamification
    public function badges()
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
            ->withPivot('unlocked_at')
            ->withTimestamps();
    }

    public function points(): HasMany
    {
        return $this->hasMany(Point::class);
    }

    public function totalPoints(): int
    {
        return $this->points()->sum('points');
    }

    public function level(): int
    {
        $points = $this->totalPoints();
        return (int) floor($points / 1000) + 1; // 1000 points = 1 level
    }

    public function pointsToNextLevel(): int
    {
        $currentLevel = $this->level();
        $pointsForNextLevel = $currentLevel * 1000;
        $currentPoints = $this->totalPoints();
        return $pointsForNextLevel - $currentPoints;
    }

    // AI Providers
    public function aiProviders(): HasMany
    {
        return $this->hasMany(UserAiProvider::class);
    }

    public function savedResources(): HasMany
    {
        return $this->hasMany(SavedResource::class);
    }

    public function getActiveAiProvider(string $provider): ?UserAiProvider
    {
        return $this->aiProviders()
            ->where('provider', $provider)
            ->where('is_active', true)
            ->first();
    }

    // Exercises & Learning
    public function exercises(): HasMany
    {
        return $this->hasMany(Exercise::class);
    }

    public function exerciseAttempts(): HasMany
    {
        return $this->hasMany(ExerciseAttempt::class);
    }

    public function flashcards(): HasMany
    {
        return $this->hasMany(Flashcard::class);
    }

    public function flashcardReviews(): HasMany
    {
        return $this->hasMany(FlashcardReview::class);
    }
}
