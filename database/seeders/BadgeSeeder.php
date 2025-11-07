<?php

namespace Database\Seeders;

use App\Models\Badge;
use Illuminate\Database\Seeder;

class BadgeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $badges = [
            // Exercise Badges
            [
                'name' => 'First Steps',
                'slug' => 'first-exercise',
                'description' => 'Complete your first exercise',
                'icon' => '🎯',
                'requirement_type' => 'exercises_completed',
                'requirement_value' => 1,
            ],
            [
                'name' => 'Exercise Enthusiast',
                'slug' => 'exercise-enthusiast',
                'description' => 'Complete 10 exercises',
                'icon' => '💪',
                'requirement_type' => 'exercises_completed',
                'requirement_value' => 10,
            ],
            [
                'name' => 'Exercise Master',
                'slug' => 'exercise-master',
                'description' => 'Complete 50 exercises',
                'icon' => '🏆',
                'requirement_type' => 'exercises_completed',
                'requirement_value' => 50,
            ],
            [
                'name' => 'Exercise Legend',
                'slug' => 'exercise-legend',
                'description' => 'Complete 100 exercises',
                'icon' => '👑',
                'requirement_type' => 'exercises_completed',
                'requirement_value' => 100,
            ],

            // Material Badges
            [
                'name' => 'Knowledge Seeker',
                'slug' => 'first-material',
                'description' => 'Study your first material',
                'icon' => '📚',
                'requirement_type' => 'materials_studied',
                'requirement_value' => 1,
            ],
            [
                'name' => 'Bookworm',
                'slug' => 'bookworm',
                'description' => 'Study 25 materials',
                'icon' => '🐛',
                'requirement_type' => 'materials_studied',
                'requirement_value' => 25,
            ],
            [
                'name' => 'Scholar',
                'slug' => 'scholar',
                'description' => 'Study 100 materials',
                'icon' => '🎓',
                'requirement_type' => 'materials_studied',
                'requirement_value' => 100,
            ],

            // Flashcard Badges
            [
                'name' => 'Memory Builder',
                'slug' => 'memory-builder',
                'description' => 'Review 50 flashcards',
                'icon' => '🧠',
                'requirement_type' => 'flashcards_reviewed',
                'requirement_value' => 50,
            ],
            [
                'name' => 'Flashcard Pro',
                'slug' => 'flashcard-pro',
                'description' => 'Review 200 flashcards',
                'icon' => '🃏',
                'requirement_type' => 'flashcards_reviewed',
                'requirement_value' => 200,
            ],
            [
                'name' => 'Memory Master',
                'slug' => 'memory-master',
                'description' => 'Review 500 flashcards',
                'icon' => '🔮',
                'requirement_type' => 'flashcards_reviewed',
                'requirement_value' => 500,
            ],

            // Points Badges
            [
                'name' => 'Point Collector',
                'slug' => 'point-collector',
                'description' => 'Earn 1,000 points',
                'icon' => '⭐',
                'requirement_type' => 'points_earned',
                'requirement_value' => 1000,
            ],
            [
                'name' => 'Point Hunter',
                'slug' => 'point-hunter',
                'description' => 'Earn 5,000 points',
                'icon' => '🌟',
                'requirement_type' => 'points_earned',
                'requirement_value' => 5000,
            ],
            [
                'name' => 'Point Legend',
                'slug' => 'point-legend',
                'description' => 'Earn 10,000 points',
                'icon' => '💎',
                'requirement_type' => 'points_earned',
                'requirement_value' => 10000,
            ],

            // Streak Badges
            [
                'name' => 'Consistent Learner',
                'slug' => 'streak-7',
                'description' => 'Study for 7 days in a row',
                'icon' => '🔥',
                'requirement_type' => 'streak_days',
                'requirement_value' => 7,
            ],
            [
                'name' => 'Dedication Champion',
                'slug' => 'streak-30',
                'description' => 'Study for 30 days in a row',
                'icon' => '🚀',
                'requirement_type' => 'streak_days',
                'requirement_value' => 30,
            ],
            [
                'name' => 'Unstoppable',
                'slug' => 'streak-100',
                'description' => 'Study for 100 days in a row',
                'icon' => '⚡',
                'requirement_type' => 'streak_days',
                'requirement_value' => 100,
            ],

            // Social Badges
            [
                'name' => 'Team Player',
                'slug' => 'first-group',
                'description' => 'Join your first study group',
                'icon' => '👥',
                'requirement_type' => 'groups_joined',
                'requirement_value' => 1,
            ],
            [
                'name' => 'Social Butterfly',
                'slug' => 'social-butterfly',
                'description' => 'Join 5 study groups',
                'icon' => '🦋',
                'requirement_type' => 'groups_joined',
                'requirement_value' => 5,
            ],

            // Mind Map Badges
            [
                'name' => 'Visual Thinker',
                'slug' => 'first-mindmap',
                'description' => 'Create your first mind map',
                'icon' => '🗺️',
                'requirement_type' => 'mind_maps_created',
                'requirement_value' => 1,
            ],
            [
                'name' => 'Mind Mapper',
                'slug' => 'mind-mapper',
                'description' => 'Create 10 mind maps',
                'icon' => '🎨',
                'requirement_type' => 'mind_maps_created',
                'requirement_value' => 10,
            ],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }
    }
}
