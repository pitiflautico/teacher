@php
    $data = $this->getData();
@endphp

<x-filament-widgets::widget>
    <!-- Main welcome card with soft gradient -->
    <div class="bg-gradient-to-br from-purple-400 to-purple-500 rounded-3xl p-8 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex items-center justify-between">
            <!-- Left content -->
            <div class="flex-1">
                <h2 class="text-3xl font-bold mb-3">
                    {{ __('Welcome back') }}, {{ $data['name'] }}! 👋
                </h2>
                <p class="text-purple-50 text-base mb-6 max-w-md leading-relaxed">
                    {{ __('Ready to continue your learning journey? Let\'s make today count!') }}
                </p>

                <!-- CTA Button -->
                <a href="{{ route('filament.admin.pages.upload-homework') }}"
                   class="inline-flex items-center gap-2 bg-white text-purple-600 font-semibold px-6 py-3 rounded-full hover:bg-purple-50 transition-all shadow-md hover:shadow-lg">
                    <span>{{ __('Upload Homework') }}</span>
                    <x-heroicon-m-arrow-right class="w-5 h-5" />
                </a>
            </div>

            <!-- Right side - Level badge -->
            <div class="ml-8">
                <div class="bg-white/20 backdrop-blur-sm rounded-2xl px-8 py-6 border border-white/30 text-center">
                    <span class="text-sm font-medium text-purple-100 uppercase tracking-wide">{{ __('Level') }}</span>
                    <div class="text-5xl font-bold mt-2">{{ $data['level'] }}</div>
                    <div class="mt-3 flex items-center justify-center gap-1">
                        @for($i = 0; $i < min($data['badges_count'], 3); $i++)
                            <div class="w-2 h-2 bg-yellow-300 rounded-full"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats grid with clean white cards -->
    <div class="flex flex-wrap gap-4 mt-4">
        <!-- Total Points -->
        <div class="flex-1 min-w-[200px] bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Points') }}</span>
                <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center">
                    <x-heroicon-s-star class="w-5 h-5 text-yellow-600 dark:text-yellow-400" />
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($data['total_points']) }}</p>
        </div>

        <!-- Subjects -->
        <div class="flex-1 min-w-[200px] bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Subjects') }}</span>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center">
                    <x-heroicon-s-book-open class="w-5 h-5 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $data['subjects_count'] }}</p>
        </div>

        <!-- Exercises Completed -->
        <div class="flex-1 min-w-[200px] bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Completed') }}</span>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center">
                    <x-heroicon-s-check-circle class="w-5 h-5 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($data['exercises_completed']) }}</p>
        </div>

        <!-- Study Streak -->
        <div class="flex-1 min-w-[200px] bg-white dark:bg-gray-800 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ __('Streak') }}</span>
                <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 rounded-xl flex items-center justify-center">
                    <x-heroicon-s-fire class="w-5 h-5 text-orange-600 dark:text-orange-400" />
                </div>
            </div>
            <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $data['study_streak'] }} <span class="text-lg font-normal text-gray-500">{{ __('days') }}</span></p>
        </div>
    </div>
</x-filament-widgets::widget>
