<x-filament-panels::page>
    @if ($exercise)
        <div class="space-y-6">
            {{-- Exercise Timer (if time limit exists) --}}
            @if ($exercise->time_limit)
                <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-warning-800 dark:text-warning-300">
                            Time Limit: {{ gmdate('i:s', $exercise->time_limit) }}
                        </span>
                        <span class="text-sm text-warning-600 dark:text-warning-400" id="timer">
                            --:--
                        </span>
                    </div>
                </div>
            @endif

            {{-- Math Support Notice --}}
            @if ($exercise->contains_math)
                <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-lg p-4">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-information-circle class="w-5 h-5 text-info-600 dark:text-info-400" />
                        <span class="text-sm text-info-800 dark:text-info-300">
                            This exercise contains mathematical formulas
                        </span>
                    </div>
                </div>
            @endif

            {{-- Hints (collapsible) --}}
            @if ($exercise->hints)
                <details class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                    <summary class="cursor-pointer font-medium text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <x-heroicon-o-light-bulb class="w-5 h-5" />
                        Need a hint?
                    </summary>
                    <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">
                        {{ $exercise->hints }}
                    </div>
                </details>
            @endif

            {{-- Form --}}
            <form wire:submit="submit">
                {{ $this->form }}

                <div class="mt-6 flex gap-4">
                    <x-filament::button
                        type="submit"
                        color="success"
                        size="lg"
                        icon="heroicon-o-check-circle"
                    >
                        Submit Answer
                    </x-filament::button>

                    <x-filament::button
                        wire:click="skip"
                        color="gray"
                        size="lg"
                        icon="heroicon-o-forward"
                    >
                        Skip
                    </x-filament::button>
                </div>
            </form>

            {{-- Statistics --}}
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 rounded-lg p-4">
                    <div class="text-sm text-success-600 dark:text-success-400">Total Points Earned</div>
                    <div class="text-2xl font-bold text-success-900 dark:text-success-100">
                        {{ auth()->user()->attempts()->sum('score') }}
                    </div>
                </div>

                <div class="bg-info-50 dark:bg-info-900/20 border border-info-200 dark:border-info-800 rounded-lg p-4">
                    <div class="text-sm text-info-600 dark:text-info-400">Correct Answers</div>
                    <div class="text-2xl font-bold text-info-900 dark:text-info-100">
                        {{ auth()->user()->attempts()->where('is_correct', true)->count() }}
                    </div>
                </div>

                <div class="bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-800 rounded-lg p-4">
                    <div class="text-sm text-warning-600 dark:text-warning-400">Accuracy</div>
                    <div class="text-2xl font-bold text-warning-900 dark:text-warning-100">
                        @php
                            $total = auth()->user()->attempts()->count();
                            $correct = auth()->user()->attempts()->where('is_correct', true)->count();
                            $accuracy = $total > 0 ? round(($correct / $total) * 100) : 0;
                        @endphp
                        {{ $accuracy }}%
                    </div>
                </div>
            </div>
        </div>

        @if ($exercise->time_limit)
            <script>
                let timeLimit = {{ $exercise->time_limit }};
                let startTime = {{ $startTime }};
                let timerElement = document.getElementById('timer');

                function updateTimer() {
                    let elapsed = Math.floor(Date.now() / 1000) - startTime;
                    let remaining = Math.max(0, timeLimit - elapsed);

                    let minutes = Math.floor(remaining / 60);
                    let seconds = remaining % 60;

                    timerElement.textContent =
                        String(minutes).padStart(2, '0') + ':' +
                        String(seconds).padStart(2, '0');

                    if (remaining === 0) {
                        @this.call('submit');
                    }
                }

                setInterval(updateTimer, 1000);
                updateTimer();
            </script>
        @endif
    @else
        <div class="text-center py-12">
            <x-heroicon-o-academic-cap class="mx-auto h-12 w-12 text-gray-400" />
            <h3 class="mt-2 text-sm font-semibold text-gray-900 dark:text-gray-100">No exercises available</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Check back later for more exercises.
            </p>
        </div>
    @endif
</x-filament-panels::page>
