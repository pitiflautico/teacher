<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Search Form --}}
        <form wire:submit="search" class="space-y-4">
            {{ $this->form }}

            <div class="flex gap-3">
                <x-filament::button
                    type="submit"
                    icon="heroicon-o-magnifying-glass"
                    color="primary"
                >
                    Search
                </x-filament::button>

                @if(!empty($results))
                    <x-filament::button
                        type="button"
                        wire:click="saveAllResults"
                        icon="heroicon-o-bookmark"
                        color="success"
                    >
                        Save All Results
                    </x-filament::button>

                    <x-filament::button
                        type="button"
                        wire:click="clearResults"
                        icon="heroicon-o-x-mark"
                        color="danger"
                        outlined
                    >
                        Clear
                    </x-filament::button>
                @endif
            </div>
        </form>

        {{-- Results --}}
        @if(!empty($results))
            <div class="space-y-3">
                <h2 class="text-lg font-semibold">
                    Search Results ({{ count($results) }})
                </h2>

                <div class="grid gap-4">
                    @foreach($results as $index => $result)
                        <div class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 p-4 hover:shadow-md transition">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1 space-y-2">
                                    <h3 class="font-semibold text-gray-900 dark:text-gray-100">
                                        {{ $result['title'] }}
                                    </h3>

                                    @if(isset($result['snippet']))
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2">
                                            {{ $result['snippet'] }}
                                        </p>
                                    @endif

                                    <div class="flex items-center gap-3 text-xs text-gray-500 dark:text-gray-500">
                                        <a
                                            href="{{ $result['url'] }}"
                                            target="_blank"
                                            class="flex items-center gap-1 hover:text-primary-600 dark:hover:text-primary-400"
                                        >
                                            <x-heroicon-o-link class="w-4 h-4" />
                                            {{ parse_url($result['url'], PHP_URL_HOST) }}
                                        </a>

                                        @if(isset($result['source']))
                                            <span class="flex items-center gap-1">
                                                <x-heroicon-o-globe-alt class="w-4 h-4" />
                                                {{ ucfirst($result['source']) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div>
                                    @if(in_array($index, $selectedResults))
                                        <x-filament::badge color="success">
                                            <x-heroicon-o-check class="w-4 h-4 mr-1" />
                                            Saved
                                        </x-filament::badge>
                                    @else
                                        <x-filament::button
                                            wire:click="saveResult({{ $index }})"
                                            icon="heroicon-o-bookmark"
                                            color="primary"
                                            size="sm"
                                        >
                                            Save
                                        </x-filament::button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif($query)
            <div class="text-center py-12">
                <x-heroicon-o-magnifying-glass class="w-12 h-12 mx-auto text-gray-400" />
                <p class="mt-4 text-gray-600 dark:text-gray-400">
                    Click "Search" to find educational content
                </p>
            </div>
        @endif
    </div>
</x-filament-panels::page>
