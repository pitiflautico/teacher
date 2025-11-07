<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <x-heroicon-o-language class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    {{ __('Language') }}
                </span>
            </div>

            <div class="flex gap-2">
                <button
                    wire:click="switchLanguage('en')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                        {{ app()->getLocale() === 'en'
                            ? 'bg-primary-500 text-white'
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
                        }}"
                >
                    🇬🇧 English
                </button>

                <button
                    wire:click="switchLanguage('es')"
                    class="px-3 py-1.5 text-sm font-medium rounded-lg transition-colors
                        {{ app()->getLocale() === 'es'
                            ? 'bg-primary-500 text-white'
                            : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700'
                        }}"
                >
                    🇪🇸 Español
                </button>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
