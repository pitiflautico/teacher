<?php

namespace App\Notifications;

use App\Models\Material;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaterialProcessedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Material $material,
        public bool $success = true,
        public ?string $error = null
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        if ($this->success) {
            return (new MailMessage)
                ->subject('Material Processed Successfully')
                ->greeting("Hello {$notifiable->name}!")
                ->line("Your material '{$this->material->title}' has been processed successfully.")
                ->line("The OCR extraction and AI analysis are complete.")
                ->action('View Material', url("/admin/materials/{$this->material->id}"))
                ->line('You can now generate exercises from this material.');
        } else {
            return (new MailMessage)
                ->error()
                ->subject('Material Processing Failed')
                ->greeting("Hello {$notifiable->name}!")
                ->line("There was an error processing your material '{$this->material->title}'.")
                ->line("Error: {$this->error}")
                ->action('View Material', url("/admin/materials/{$this->material->id}"))
                ->line('Please try again or contact support if the problem persists.');
        }
    }

    public function toArray(object $notifiable): array
    {
        return [
            'material_id' => $this->material->id,
            'material_title' => $this->material->title,
            'success' => $this->success,
            'error' => $this->error,
            'type' => 'material_processed',
        ];
    }
}
