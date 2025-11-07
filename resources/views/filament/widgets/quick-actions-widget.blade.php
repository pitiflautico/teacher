<x-filament-widgets::widget>
    <div class="space-y-4">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0 w-10 h-10 bg-primary-100 dark:bg-primary-900 rounded-lg flex items-center justify-center">
                <x-heroicon-o-bolt class="w-6 h-6 text-primary-600 dark:text-primary-400" />
            </div>
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">
                    {{ __('Quick Actions') }}
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    {{ __('Get started with your learning') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($this->getActions() as $action)
                <a href="{{ $action['url'] }}"
                   class="group relative p-6 bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-xl border-2 border-gray-200 dark:border-gray-700 hover:border-{{ $action['color'] }}-500 dark:hover:border-{{ $action['color'] }}-500 transition-all hover:shadow-lg cursor-pointer">

                    <!-- Icon -->
                    <div class="mb-4">
                        <div class="inline-flex items-center justify-center w-14 h-14 rounded-xl bg-{{ $action['color'] }}-100 dark:bg-{{ $action['color'] }}-900/30 group-hover:bg-{{ $action['color'] }}-500 transition-all">
                            @php
                                $iconColor = "text-{$action['color']}-600 dark:text-{$action['color']}-400 group-hover:text-white";
                            @endphp
                            <x-dynamic-component
                                :component="$action['icon']"
                                class="w-7 h-7 {{ $iconColor }} transition-colors"
                            />
                        </div>
                    </div>

                    <!-- Content -->
                    <div>
                        <h3 class="font-bold text-gray-900 dark:text-white mb-1 group-hover:text-{{ $action['color'] }}-600 dark:group-hover:text-{{ $action['color'] }}-400 transition-colors">
                            {{ $action['label'] }}
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            {{ $action['description'] }}
                        </p>
                    </div>

                    <!-- Arrow indicator -->
                    <div class="absolute bottom-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                        <x-heroicon-m-arrow-right class="w-5 h-5 text-{{ $action['color'] }}-600 dark:text-{{ $action['color'] }}-400" />
                    </div>

                    <!-- Hover effect overlay -->
                    <div class="absolute inset-0 rounded-xl bg-gradient-to-br from-{{ $action['color'] }}-500/0 to-{{ $action['color'] }}-500/0 group-hover:from-{{ $action['color'] }}-500/5 group-hover:to-{{ $action['color'] }}-500/10 transition-all pointer-events-none"></div>
                </a>
            @endforeach
        </div>
    </div>

    <style>
        /* Dynamic color generation for hover states */
        @layer components {
            .hover\:border-primary-500:hover {
                border-color: rgb(139 92 246);
            }
            .hover\:border-success-500:hover {
                border-color: rgb(34 197 94);
            }
            .hover\:border-warning-500:hover {
                border-color: rgb(251 191 36);
            }
            .hover\:border-info-500:hover {
                border-color: rgb(59 130 246);
            }

            .bg-primary-100 {
                background-color: rgb(237 233 254);
            }
            .dark .dark\:bg-primary-900\/30 {
                background-color: rgb(76 29 149 / 0.3);
            }
            .group:hover .group-hover\:bg-primary-500 {
                background-color: rgb(139 92 246);
            }

            .bg-success-100 {
                background-color: rgb(220 252 231);
            }
            .dark .dark\:bg-success-900\/30 {
                background-color: rgb(20 83 45 / 0.3);
            }
            .group:hover .group-hover\:bg-success-500 {
                background-color: rgb(34 197 94);
            }

            .bg-warning-100 {
                background-color: rgb(254 243 199);
            }
            .dark .dark\:bg-warning-900\/30 {
                background-color: rgb(120 53 15 / 0.3);
            }
            .group:hover .group-hover\:bg-warning-500 {
                background-color: rgb(251 191 36);
            }

            .bg-info-100 {
                background-color: rgb(219 234 254);
            }
            .dark .dark\:bg-info-900\/30 {
                background-color: rgb(30 58 138 / 0.3);
            }
            .group:hover .group-hover\:bg-info-500 {
                background-color: rgb(59 130 246);
            }
        }
    </style>
</x-filament-widgets::widget>
