<?php

namespace App\Jobs;

use App\Models\Material;
use App\Models\Flashcard;
use App\Services\AI\AIManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Filament\Notifications\Notification;

class GenerateFlashcardsFromMaterial implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Material $material,
        public int $count = 10
    ) {}

    public function handle(): void
    {
        try {
            $aiManager = app(AIManager::class);

            $content = $this->material->extracted_text ?? $this->material->description ?? '';

            if (empty($content)) {
                Log::warning('No content to generate flashcards from', ['material_id' => $this->material->id]);
                return;
            }

            $prompt = "Eres un asistente educativo experto en crear tarjetas de estudio (flashcards).

Genera {$this->count} flashcards del siguiente contenido educativo.

Cada flashcard debe tener:
- Front: Una pregunta o concepto clave (máximo 150 caracteres)
- Back: La respuesta o explicación completa (máximo 500 caracteres)
- Hint (opcional): Una pista útil

Las flashcards deben:
- Cubrir los conceptos más importantes
- Ser claras y concisas
- Ser útiles para memorización y repaso
- Evitar redundancia

Contenido:
{$content}

Responde ÚNICAMENTE con un JSON válido en este formato exacto:
{
  \"flashcards\": [
    {
      \"front\": \"Pregunta o concepto\",
      \"back\": \"Respuesta o explicación\",
      \"hint\": \"Pista opcional\"
    }
  ]
}";

            $response = $aiManager->complete($prompt, [
                'max_tokens' => 2000,
                'temperature' => 0.8,
            ]);

            // Parse JSON response
            $data = json_decode($response->content, true);

            if (!isset($data['flashcards']) || !is_array($data['flashcards'])) {
                throw new \Exception('Invalid JSON response from AI');
            }

            $created = 0;
            foreach ($data['flashcards'] as $flashcardData) {
                if (!isset($flashcardData['front']) || !isset($flashcardData['back'])) {
                    continue;
                }

                Flashcard::create([
                    'user_id' => $this->material->user_id,
                    'subject_id' => $this->material->subject_id,
                    'topic_id' => $this->material->topic_id,
                    'front' => substr($flashcardData['front'], 0, 255),
                    'back' => $flashcardData['back'],
                    'hint' => $flashcardData['hint'] ?? null,
                    'difficulty' => 'medium',
                    'tags' => json_encode(['ai-generated', 'material-' . $this->material->id]),
                    // SM-2 algorithm defaults
                    'easiness_factor' => 2.5,
                    'interval' => 0,
                    'repetitions' => 0,
                    'next_review_at' => now(),
                ]);

                $created++;
            }

            // Enviar notificación al usuario
            Notification::make()
                ->title('Flashcards generadas')
                ->success()
                ->body("Se generaron {$created} flashcards desde '{$this->material->title}'")
                ->sendToDatabase($this->material->user);

            Log::info('Flashcards generated successfully', [
                'material_id' => $this->material->id,
                'count' => $created,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate flashcards', [
                'material_id' => $this->material->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
