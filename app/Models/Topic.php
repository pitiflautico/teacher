<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Topic extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'subject_id',
        'name',
        'description',
        'order',
        'is_completed',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'order' => 'integer',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }
}
