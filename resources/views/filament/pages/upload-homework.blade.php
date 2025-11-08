<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <!-- Hero Section -->
        <div class="mb-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 mb-4 rounded-full bg-primary-100 dark:bg-primary-900/30">
                <x-heroicon-o-cloud-arrow-up class="w-8 h-8 text-primary-600 dark:text-primary-400" />
            </div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                Upload Your Homework
            </h1>
            <p class="text-gray-600 dark:text-gray-400">
                Easy 3-step process to upload and organize your homework
            </p>
        </div>

        <!-- Wizard Form -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <form wire:submit="submit">
                {{ $this->form }}
            </form>
        </div>

        <!-- Help Section -->
        <div class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex items-start gap-3">
                <x-heroicon-o-information-circle class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" />
                <div>
                    <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-1">Need Help?</h3>
                    <p class="text-sm text-blue-800 dark:text-blue-200">
                        After uploading, we'll automatically extract text from your documents and help you generate exercises and flashcards!
                    </p>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Wizard step styling */
        .fi-fo-wizard-step-label {
            @apply font-semibold;
        }

        .fi-fo-wizard-step-description {
            @apply text-sm text-gray-600 dark:text-gray-400;
        }

        /* Make wizard more prominent */
        .fi-fo-wizard {
            @apply space-y-6;
        }
    </style>
</x-filament-panels::page>
