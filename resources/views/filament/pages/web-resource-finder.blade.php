<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Search Form -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <form wire:submit="search">
                {{ $this->form }}

                <div class="mt-6">
                    <x-filament::button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="w-full sm:w-auto"
                    >
                        <x-heroicon-m-magnifying-glass class="w-5 h-5 mr-2" />
                        <span wire:loading.remove>{{ __('Search Resources') }}</span>
                        <span wire:loading>{{ __('Searching...') }}</span>
                    </x-filament::button>
                </div>
            </form>
        </div>

        <!-- Loading State -->
        @if($searching)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-full mb-4">
                    <svg class="animate-spin h-8 w-8 text-purple-600 dark:text-purple-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <p class="text-lg font-medium text-gray-700 dark:text-gray-300">{{ __('Searching for resources...') }}</p>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('This may take a few seconds') }}</p>
            </div>
        @endif

        <!-- Results Section -->
        @if(!$searching && !empty($results))
            <div>
                <!-- Results Header -->
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">
                        {{ __('Found :count resources', ['count' => count($results)]) }}
                    </h2>

                    @if(!empty($selected))
                        <x-filament::button
                            wire:click="saveSelected"
                            color="success"
                        >
                            <x-heroicon-m-bookmark class="w-5 h-5 mr-2" />
                            {{ __('Save :count Selected', ['count' => count($selected)]) }}
                        </x-filament::button>
                    @endif
                </div>

                <!-- Results Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($results as $index => $result)
                        <div
                            wire:click="toggleSelect({{ $index }})"
                            class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border-2 transition-all cursor-pointer hover:shadow-md
                                {{ in_array($index, $selected)
                                    ? 'border-purple-500 bg-purple-50 dark:bg-purple-900/20'
                                    : 'border-gray-100 dark:border-gray-700 hover:border-purple-200 dark:hover:border-purple-800'
                                }}"
                        >
                            <div class="p-5">
                                <!-- Header with icon and type badge -->
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex items-start gap-3 flex-1">
                                        <!-- Type Icon -->
                                        @php
                                            $iconBgClass = match($result['type']) {
                                                'pdf' => 'bg-red-100 dark:bg-red-900/30',
                                                'video' => 'bg-blue-100 dark:bg-blue-900/30',
                                                'exercise' => 'bg-green-100 dark:bg-green-900/30',
                                                default => 'bg-gray-100 dark:bg-gray-700'
                                            };
                                        @endphp
                                        <div class="shrink-0 w-12 h-12 rounded-xl flex items-center justify-center {{ $iconBgClass }}">
                                            @if($result['type'] === 'pdf')
                                                <x-heroicon-o-document-text class="w-6 h-6 text-red-600 dark:text-red-400" />
                                            @elseif($result['type'] === 'video')
                                                <x-heroicon-o-play-circle class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                                            @elseif($result['type'] === 'exercise')
                                                <x-heroicon-o-pencil-square class="w-6 h-6 text-green-600 dark:text-green-400" />
                                            @else
                                                <x-heroicon-o-document class="w-6 h-6 text-gray-600 dark:text-gray-400" />
                                            @endif
                                        </div>

                                        <!-- Title and snippet -->
                                        <div class="flex-1 min-w-0">
                                            <h3 class="font-semibold text-gray-900 dark:text-white line-clamp-2 mb-1">
                                                {{ $result['title'] }}
                                            </h3>
                                            @if(!empty($result['snippet']))
                                                <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                                    {{ $result['snippet'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Selection checkbox -->
                                    <div class="ml-3 shrink-0">
                                        <div class="w-6 h-6 rounded-md border-2 flex items-center justify-center transition-colors
                                            {{ in_array($index, $selected)
                                                ? 'border-purple-500 bg-purple-500'
                                                : 'border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700'
                                            }}">
                                            @if(in_array($index, $selected))
                                                <x-heroicon-m-check class="w-4 h-4 text-white" />
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- URL and metadata -->
                                <div class="flex items-center justify-between gap-3 mt-4 pt-4 border-t border-gray-100 dark:border-gray-700">
                                    <a
                                        href="{{ $result['url'] }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        onclick="event.stopPropagation()"
                                        class="text-sm text-purple-600 dark:text-purple-400 hover:text-purple-700 dark:hover:text-purple-300 truncate flex-1 flex items-center gap-1"
                                    >
                                        <x-heroicon-m-arrow-top-right-on-square class="w-4 h-4 shrink-0" />
                                        <span class="truncate">{{ parse_url($result['url'], PHP_URL_HOST) }}</span>
                                    </a>

                                    <!-- AI Relevance Score -->
                                    @if(!empty($result['relevance']) && $result['relevance'] > 0)
                                        @php
                                            $scoreColorClass = $result['relevance'] >= 80
                                                ? 'text-green-600 dark:text-green-400'
                                                : ($result['relevance'] >= 60
                                                    ? 'text-blue-600 dark:text-blue-400'
                                                    : 'text-gray-600 dark:text-gray-400');
                                        @endphp
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <x-heroicon-m-sparkles class="w-4 h-4 text-purple-500" />
                                            <span class="text-sm font-semibold {{ $scoreColorClass }}">
                                                {{ $result['relevance'] }}%
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                <!-- AI Reason (if available) -->
                                @if(!empty($result['ai_reason']))
                                    <div class="mt-3 p-3 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                        <div class="flex items-start gap-2">
                                            <x-heroicon-m-light-bulb class="w-4 h-4 text-purple-600 dark:text-purple-400 mt-0.5 shrink-0" />
                                            <p class="text-xs text-purple-700 dark:text-purple-300">
                                                {{ $result['ai_reason'] }}
                                            </p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Empty State (no search performed yet) -->
        @if(!$searching && empty($results) && empty($this->data['query']))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 dark:bg-purple-900/30 rounded-full mb-4">
                    <x-heroicon-o-magnifying-glass class="w-10 h-10 text-purple-600 dark:text-purple-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('Search for Educational Resources') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    {{ __('Enter a topic or subject to find exercises, PDFs, videos, and study materials from across the web. Our AI will help rank the best results for you.') }}
                </p>
            </div>
        @endif

        <!-- Empty State (search performed but no results) -->
        @if(!$searching && empty($results) && !empty($this->data['query']))
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 dark:bg-yellow-900/30 rounded-full mb-4">
                    <x-heroicon-o-exclamation-triangle class="w-10 h-10 text-yellow-600 dark:text-yellow-400" />
                </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ __('No results found') }}
                </h3>
                <p class="text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                    {{ __('Try different keywords, adjust your filters, or disable AI filtering for broader results.') }}
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
