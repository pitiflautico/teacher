<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExercisesGeneratedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $count,
        public string $type,
        public string $difficulty,
        public ?string $sourceName = null,
        public bool $success = true
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
                ->subject('Exercises Generated Successfully')
                ->greeting("Hello {$notifiable->name}!")
                ->line("Successfully generated {$this->count} {$this->difficulty} {$this->type} exercises" .
                      ($this->sourceName ? " from {$this->sourceName}" : "") . ".")
                ->action('View Exercises', url('/admin/exercises'))
                ->line('The exercises are now active and ready for students!');
        } else {
            return (new MailMessage)
                ->error()
                ->subject('Exercise Generation Failed')
                ->greeting("Hello {$notifiable->name}!")
                ->line("There was an error generating exercises.")
                ->action('Try Again', url('/admin/materials'))
                ->line('Please try again or contact support if the problem persists.');
        }
    }

    public function toArray(object $notifiable): array
    {
        return [
            'count' => $this->count,
            'type' => $this->type,
            'difficulty' => $this->difficulty,
            'source_name' => $this->sourceName,
            'success' => $this->success,
            'notification_type' => 'exercises_generated',
        ];
    }
}
