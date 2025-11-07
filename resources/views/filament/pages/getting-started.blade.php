<x-filament-panels::page>
    <div class="max-w-5xl mx-auto">
        <!-- Wizard Form -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-8">
            <form wire:submit="submit">
                {{ $this->form }}
            </form>
        </div>
    </div>

    <style>
        /* Enhanced wizard styling */
        .fi-fo-wizard {
            @apply space-y-8;
        }

        .fi-fo-wizard-header {
            @apply mb-8;
        }

        .fi-fo-wizard-step {
            @apply transition-all duration-200;
        }

        .fi-fo-wizard-step-label {
            @apply text-lg font-bold;
        }

        .fi-fo-wizard-step-description {
            @apply text-sm;
        }

        /* Make sections look better */
        .fi-section {
            @apply border-0 shadow-none p-0;
        }
    </style>
</x-filament-panels::page>
