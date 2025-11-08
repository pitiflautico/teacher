<?php

namespace App\Jobs;

use App\Models\Material;
use App\Services\AI\AIManager;
use App\Notifications\MaterialProcessedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class GenerateSummaryFromMaterial implements ShouldQueue
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
                Log::warning('No content to summarize', ['material_id' => $this->material->id]);
                return;
            }

            $prompt = "Eres un asistente educativo experto. Genera un resumen completo y estructurado del siguiente contenido educativo.

El resumen debe:
- Ser claro y conciso
- Destacar los puntos clave y conceptos importantes
- Estar organizado con viñetas o párrafos según corresponda
- Ser útil para estudio y repaso
- Mantener la información más relevante

Contenido a resumir:
{$content}

Genera el resumen en formato markdown:";

            $response = $aiManager->complete($prompt, [
                'max_tokens' => 2000,
                'temperature' => 0.7,
            ]);

            // Crear un nuevo Material con el resumen
            $summary = Material::create([
                'user_id' => $this->material->user_id,
                'subject_id' => $this->material->subject_id,
                'topic_id' => $this->material->topic_id,
                'title' => 'Resumen: ' . $this->material->title,
                'description' => 'Resumen generado automáticamente con IA',
                'type' => 'note',
                'extracted_text' => $response->content,
                'is_processed' => true,
                'processed_at' => now(),
                'ai_metadata' => json_encode([
                    'generated_from' => 'summary',
                    'source_material_id' => $this->material->id,
                    'provider' => $response->provider,
                    'model' => $response->metadata['model'] ?? 'unknown',
                    'tokens_used' => $response->totalTokens,
                ]),
            ]);

            // Notificar al usuario
            $this->material->user->notify(
                new MaterialProcessedNotification($summary, 'Resumen generado con éxito')
            );

            Log::info('Summary generated successfully', [
                'source_material_id' => $this->material->id,
                'summary_material_id' => $summary->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate summary', [
                'material_id' => $this->material->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
