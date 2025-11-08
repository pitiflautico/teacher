<?php

namespace App\Jobs;

use App\Models\Material;
use App\Services\AI\AIManager;
use App\Notifications\MaterialProcessedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateNotesFromMaterial implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Material $material
    ) {}

    public function handle(): void
    {
        try {
            $aiManager = app(AIManager::class);

            $content = $this->material->extracted_text ?? $this->material->description ?? '';

            if (empty($content)) {
                Log::warning('No content to generate notes from', ['material_id' => $this->material->id]);
                return;
            }

            $prompt = "Eres un asistente educativo experto. Genera unos apuntes completos y bien estructurados del siguiente contenido educativo.

Los apuntes deben:
- Estar organizados con títulos y subtítulos claros
- Incluir definiciones, conceptos clave y ejemplos
- Usar viñetas y listas para mejor comprensión
- Destacar información importante
- Ser útiles para estudio y preparación de exámenes
- Incluir fórmulas o diagramas en formato markdown/LaTeX si aplica

Contenido para generar apuntes:
{$content}

Genera apuntes estructurados en formato markdown:";

            $response = $aiManager->complete($prompt, [
                'max_tokens' => 3000,
                'temperature' => 0.7,
            ]);

            // Crear un nuevo Material con los apuntes
            $notes = Material::create([
                'user_id' => $this->material->user_id,
                'subject_id' => $this->material->subject_id,
                'topic_id' => $this->material->topic_id,
                'title' => 'Apuntes: ' . $this->material->title,
                'description' => 'Apuntes generados automáticamente con IA',
                'type' => 'note',
                'extracted_text' => $response->content,
                'is_processed' => true,
                'processed_at' => now(),
                'ai_metadata' => json_encode([
                    'generated_from' => 'notes',
                    'source_material_id' => $this->material->id,
                    'provider' => $response->provider,
                    'model' => $response->metadata['model'] ?? 'unknown',
                    'tokens_used' => $response->totalTokens,
                ]),
            ]);

            // Notificar al usuario
            $this->material->user->notify(
                new MaterialProcessedNotification($notes, 'Apuntes generados con éxito')
            );

            Log::info('Notes generated successfully', [
                'source_material_id' => $this->material->id,
                'notes_material_id' => $notes->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate notes', [
                'material_id' => $this->material->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
