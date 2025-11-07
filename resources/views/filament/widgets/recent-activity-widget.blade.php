<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-clock class="w-5 h-5 text-gray-500" />
                <span>Recent Activity</span>
            </div>
        </x-slot>

        <x-slot name="description">
            Latest learning activity trends
        </x-slot>

        <div class="space-y-3">
            @forelse($this->getActivities() as $activity)
                <div class="flex items-start gap-4 p-4 rounded-xl border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        @php
                            $bgColor = match($activity['color']) {
                                'success' => 'bg-green-100 dark:bg-green-900/30',
                                'warning' => 'bg-yellow-100 dark:bg-yellow-900/30',
                                'info' => 'bg-blue-100 dark:bg-blue-900/30',
                                'danger' => 'bg-red-100 dark:bg-red-900/30',
                                default => 'bg-gray-100 dark:bg-gray-900/30'
                            };
                            $iconColor = match($activity['color']) {
                                'success' => 'text-green-600 dark:text-green-400',
                                'warning' => 'text-yellow-600 dark:text-yellow-400',
                                'info' => 'text-blue-600 dark:text-blue-400',
                                'danger' => 'text-red-600 dark:text-red-400',
                                default => 'text-gray-600 dark:text-gray-400'
                            };
                        @endphp
                        <div class="{{ $bgColor }} w-12 h-12 rounded-xl flex items-center justify-center">
                            <x-dynamic-component :component="$activity['icon']" class="w-6 h-6 {{ $iconColor }}" />
                        </div>
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1">
                                <p class="font-semibold text-gray-900 dark:text-gray-100">
                                    {{ $activity['title'] }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-gray-400 truncate">
                                    {{ $activity['description'] }}
                                </p>
                            </div>

                            <!-- Status badge -->
                            @php
                                $badgeColor = match($activity['color']) {
                                    'success' => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                                    'warning' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                                    'info' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                                    'danger' => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                                    default => 'bg-gray-100 text-gray-800 dark:bg-gray-900/50 dark:text-gray-300'
                                };
                            @endphp
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $badgeColor }} whitespace-nowrap">
                                {{ $activity['status'] }}
                            </span>
                        </div>

                        <!-- Time -->
                        <p class="text-xs text-gray-500 dark:text-gray-500 mt-1 flex items-center gap-1">
                            <x-heroicon-m-clock class="w-3 h-3" />
                            {{ $activity['time'] }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="text-center py-12">
                    <x-heroicon-o-clock class="w-12 h-12 text-gray-400 mx-auto mb-4" />
                    <p class="text-gray-500 dark:text-gray-400">No recent activity yet</p>
                    <p class="text-sm text-gray-400 dark:text-gray-500">Start learning to see your progress here</p>
                </div>
            @endforelse
        </div>

        @if($this->getActivities()->isNotEmpty())
            <div class="mt-4 text-center">
                <a href="{{ route('filament.admin.resources.exercise-attempts.index') }}"
                   class="text-sm font-medium text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300 inline-flex items-center gap-1">
                    View All Activity
                    <x-heroicon-m-arrow-right class="w-4 h-4" />
                </a>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
