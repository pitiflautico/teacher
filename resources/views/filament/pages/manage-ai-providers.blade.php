<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header with info -->
        <div class="bg-gradient-to-br from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-xl p-6 border border-primary-200 dark:border-primary-800">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 bg-primary-500 rounded-xl flex items-center justify-center">
                    <x-heroicon-o-cpu-chip class="w-7 h-7 text-white" />
                </div>
                <div class="flex-1">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">
                        {{ __('Configure Your AI Providers') }}
                    </h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        {{ __('Add your own API keys to use AI features. Your keys are encrypted and stored securely. You can set limits to control your spending.') }}
                    </p>

                    <!-- Quick guide -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-4">
                        <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <x-heroicon-m-shield-check class="w-5 h-5 text-success-600 dark:text-success-400" />
                                <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ __('Secure') }}</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ __('API keys are encrypted in the database') }}
                            </p>
                        </div>
                        <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <x-heroicon-m-banknotes class="w-5 h-5 text-warning-600 dark:text-warning-400" />
                                <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ __('Budget Control') }}</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ __('Set token and cost limits per provider') }}
                            </p>
                        </div>
                        <div class="bg-white/50 dark:bg-gray-800/50 rounded-lg p-3">
                            <div class="flex items-center gap-2 mb-1">
                                <x-heroicon-m-chart-bar class="w-5 h-5 text-info-600 dark:text-info-400" />
                                <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ __('Usage Tracking') }}</span>
                            </div>
                            <p class="text-xs text-gray-600 dark:text-gray-400">
                                {{ __('Monitor your token usage and costs') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Help section -->
        <div class="bg-info-50 dark:bg-info-900/20 rounded-lg p-4 border border-info-200 dark:border-info-800">
            <div class="flex items-start gap-3">
                <x-heroicon-o-information-circle class="w-5 h-5 text-info-600 dark:text-info-400 flex-shrink-0 mt-0.5" />
                <div class="text-sm text-info-900 dark:text-info-100">
                    <p class="font-semibold mb-1">{{ __('How to get API keys:') }}</p>
                    <ul class="space-y-1 text-info-700 dark:text-info-300">
                        <li><strong>OpenAI:</strong> <a href="https://platform.openai.com/api-keys" target="_blank" class="underline hover:no-underline">platform.openai.com/api-keys</a></li>
                        <li><strong>Anthropic (Claude):</strong> <a href="https://console.anthropic.com/settings/keys" target="_blank" class="underline hover:no-underline">console.anthropic.com/settings/keys</a></li>
                        <li><strong>Google (Gemini):</strong> <a href="https://makersuite.google.com/app/apikey" target="_blank" class="underline hover:no-underline">makersuite.google.com/app/apikey</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Table -->
        <div>
            {{ $this->table }}
        </div>
    </div>
</x-filament-panels::page>
