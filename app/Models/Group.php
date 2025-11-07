<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_id',
        'name',
        'description',
        'avatar',
        'cover_image',
        'visibility',
        'requires_approval',
        'members_count',
        'posts_count',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'group_members')
            ->withPivot('role', 'joined_at')
            ->withTimestamps();
    }

    public function groupMembers()
    {
        return $this->hasMany(GroupMember::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function isAdmin(User $user)
    {
        return $this->members()
            ->wherePivot('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function addMember(User $user, string $role = 'member')
    {
        $this->members()->attach($user->id, [
            'role' => $role,
            'joined_at' => now(),
        ]);

        $this->increment('members_count');
    }
}
