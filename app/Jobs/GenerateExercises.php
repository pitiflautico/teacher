<?php

namespace App\Jobs;

use App\Models\Material;
use App\Models\Topic;
use App\Services\AI\ExerciseGenerator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateExercises implements ShouldQueue
{
    use Queueable;

    public $timeout = 300;
    public $tries = 2;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?Material $material = null,
        public ?Topic $topic = null,
        public string $type = 'multiple_choice',
        public string $difficulty = 'medium',
        public int $count = 5
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ExerciseGenerator $generator): void
    {
        try {
            if ($this->material) {
                $exercises = $generator->generateFromMaterial(
                    $this->material,
                    $this->type,
                    $this->difficulty,
                    $this->count
                );
            } elseif ($this->topic) {
                $exercises = $generator->generateFromTopic(
                    $this->topic,
                    $this->type,
                    $this->difficulty,
                    $this->count
                );
            } else {
                Log::error('GenerateExercises: No material or topic provided');
                return;
            }

            Log::info('Exercises generated successfully', [
                'count' => count($exercises),
                'type' => $this->type,
                'difficulty' => $this->difficulty,
            ]);
        } catch (\Exception $e) {
            Log::error('Exercise generation failed', [
                'error' => $e->getMessage(),
                'material_id' => $this->material?->id,
                'topic_id' => $this->topic?->id,
            ]);

            throw $e;
        }
    }
}
