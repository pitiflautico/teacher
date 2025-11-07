<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MindMap extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'subject_id',
        'topic_id',
        'material_id',
        'title',
        'description',
        'nodes_data',
        'edges_data',
        'thumbnail',
        'is_public',
        'views_count',
    ];

    protected $casts = [
        'nodes_data' => 'array',
        'edges_data' => 'array',
        'is_public' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Generate flashcards from this mind map
     */
    public function generateFlashcards()
    {
        $nodes = $this->nodes_data ?? [];
        $flashcards = [];

        foreach ($nodes as $node) {
            // Each node becomes a flashcard
            $flashcard = Flashcard::create([
                'user_id' => $this->user_id,
                'subject_id' => $this->subject_id,
                'topic_id' => $this->topic_id,
                'front' => $node['label'] ?? 'Concept',
                'back' => $node['description'] ?? 'Review the mind map for details',
                'easiness_factor' => 250,
                'interval' => 0,
                'repetitions' => 0,
                'next_review_at' => now(),
            ]);

            $flashcards[] = $flashcard;
        }

        return $flashcards;
    }

    public function incrementViews()
    {
        $this->increment('views_count');
    }
}
