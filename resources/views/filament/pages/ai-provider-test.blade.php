<x-filament-panels::page>
    <form wire:submit="testAll">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" size="lg">
                <x-filament::icon icon="heroicon-m-play" class="h-5 w-5 mr-2" />
                Test All Providers
            </x-filament::button>
        </div>
    </form>

    @if($openaiResult || $togetherResult || $replicateResult)
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- OpenAI Results -->
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-sparkles" class="h-5 w-5 text-blue-500" />
                        OpenAI
                    </div>
                </x-slot>

                <x-slot name="description">
                    GPT-4o-mini - Best Quality
                </x-slot>

                @if(isset($stats['openai']['error']))
                    <div class="text-red-600 dark:text-red-400">
                        Provider not configured or error occurred.
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="prose dark:prose-invert max-w-none text-sm">
                            {{ $openaiResult }}
                        </div>

                        @if(isset($stats['openai']))
                            <div class="grid grid-cols-3 gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Duration</div>
                                    <div class="font-semibold text-sm">{{ $stats['openai']['duration'] ?? 'N/A' }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Tokens</div>
                                    <div class="font-semibold text-sm">{{ $stats['openai']['tokens'] ?? 'N/A' }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Cost</div>
                                    <div class="font-semibold text-sm">{{ $stats['openai']['cost'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </x-filament::section>

            <!-- Together.ai Results -->
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-bolt" class="h-5 w-5 text-yellow-500" />
                        Together.ai
                    </div>
                </x-slot>

                <x-slot name="description">
                    Llama 3.1 - Fast & Affordable
                </x-slot>

                @if(isset($stats['together']['error']))
                    <div class="text-red-600 dark:text-red-400">
                        Provider not configured or error occurred.
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="prose dark:prose-invert max-w-none text-sm">
                            {{ $togetherResult }}
                        </div>

                        @if(isset($stats['together']))
                            <div class="grid grid-cols-3 gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Duration</div>
                                    <div class="font-semibold text-sm">{{ $stats['together']['duration'] ?? 'N/A' }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Tokens</div>
                                    <div class="font-semibold text-sm">{{ $stats['together']['tokens'] ?? 'N/A' }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Cost</div>
                                    <div class="font-semibold text-sm">{{ $stats['together']['cost'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </x-filament::section>

            <!-- Replicate Results -->
            <x-filament::section>
                <x-slot name="heading">
                    <div class="flex items-center gap-2">
                        <x-filament::icon icon="heroicon-o-cube" class="h-5 w-5 text-purple-500" />
                        Replicate
                    </div>
                </x-slot>

                <x-slot name="description">
                    Llama 2 70B - Open Source
                </x-slot>

                @if(isset($stats['replicate']['error']))
                    <div class="text-red-600 dark:text-red-400">
                        Provider not configured or error occurred.
                    </div>
                @else
                    <div class="space-y-4">
                        <div class="prose dark:prose-invert max-w-none text-sm">
                            {{ $replicateResult }}
                        </div>

                        @if(isset($stats['replicate']))
                            <div class="grid grid-cols-3 gap-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Duration</div>
                                    <div class="font-semibold text-sm">{{ $stats['replicate']['duration'] ?? 'N/A' }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Tokens</div>
                                    <div class="font-semibold text-sm">{{ $stats['replicate']['tokens'] ?? 'N/A' }}</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Cost</div>
                                    <div class="font-semibold text-sm">{{ $stats['replicate']['cost'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </x-filament::section>
        </div>

        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Comparison Tips
            </x-slot>

            <div class="prose dark:prose-invert max-w-none text-sm">
                <ul>
                    <li><strong>OpenAI (GPT-4o-mini)</strong>: Best for accuracy and complex reasoning. Higher cost but most reliable.</li>
                    <li><strong>Together.ai (Llama 3.1)</strong>: Excellent balance of speed, cost, and quality. Recommended for most educational tasks.</li>
                    <li><strong>Replicate (Llama 2)</strong>: Open-source alternative with good performance. Pay per second pricing model.</li>
                </ul>
                <p class="text-xs text-gray-600 dark:text-gray-400 mt-4">
                    💡 Tip: Try different providers to see which one works best for your use case. You can change your preferred provider in your profile settings.
                </p>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
