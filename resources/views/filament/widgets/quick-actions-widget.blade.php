<x-filament-widgets::widget>
    <div class="space-y-4">
        <!-- Header -->
        <div class="flex items-center gap-3 mb-6">
            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                <x-heroicon-o-bolt class="w-6 h-6 text-purple-600 dark:text-purple-400" />
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

        <!-- Actions Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Upload Homework -->
            <a href="{{ route('filament.admin.pages.upload-homework') }}"
               class="group relative bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 hover:border-purple-300 dark:hover:border-purple-700 hover:shadow-lg transition-all">
                <div class="flex flex-col h-full">
                    <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-500 transition-colors">
                        <x-heroicon-o-cloud-arrow-up class="w-6 h-6 text-purple-600 dark:text-purple-400 group-hover:text-white transition-colors" />
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                        {{ __('Upload Homework') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                        {{ __('Upload your notes or homework documents') }}
                    </p>
                    <div class="mt-3 flex items-center text-purple-600 dark:text-purple-400 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                        <span>{{ __('Start') }}</span>
                        <x-heroicon-m-arrow-right class="w-4 h-4 ml-1" />
                    </div>
                </div>
            </a>

            <!-- Practice Exercises -->
            <a href="{{ route('filament.admin.resources.exercises.index') }}"
               class="group relative bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 hover:border-green-300 dark:hover:border-green-700 hover:shadow-lg transition-all">
                <div class="flex flex-col h-full">
                    <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-500 transition-colors">
                        <x-heroicon-o-academic-cap class="w-6 h-6 text-green-600 dark:text-green-400 group-hover:text-white transition-colors" />
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                        {{ __('Practice Exercises') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                        {{ __('Answer questions to test your knowledge') }}
                    </p>
                    <div class="mt-3 flex items-center text-green-600 dark:text-green-400 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                        <span>{{ __('Start') }}</span>
                        <x-heroicon-m-arrow-right class="w-4 h-4 ml-1" />
                    </div>
                </div>
            </a>

            <!-- Study Flashcards -->
            <a href="{{ route('filament.admin.resources.flashcards.index') }}"
               class="group relative bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 hover:border-yellow-300 dark:hover:border-yellow-700 hover:shadow-lg transition-all">
                <div class="flex flex-col h-full">
                    <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900/30 rounded-xl flex items-center justify-center mb-4 group-hover:bg-yellow-500 transition-colors">
                        <x-heroicon-o-sparkles class="w-6 h-6 text-yellow-600 dark:text-yellow-400 group-hover:text-white transition-colors" />
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                        {{ __('Study Flashcards') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                        {{ __('Review flashcards with spaced repetition') }}
                    </p>
                    <div class="mt-3 flex items-center text-yellow-600 dark:text-yellow-400 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                        <span>{{ __('Start') }}</span>
                        <x-heroicon-m-arrow-right class="w-4 h-4 ml-1" />
                    </div>
                </div>
            </a>

            <!-- View Progress -->
            <a href="{{ route('filament.admin.pages.dashboard') }}"
               class="group relative bg-white dark:bg-gray-800 rounded-2xl p-5 border border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700 hover:shadow-lg transition-all">
                <div class="flex flex-col h-full">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-500 transition-colors">
                        <x-heroicon-o-chart-bar class="w-6 h-6 text-blue-600 dark:text-blue-400 group-hover:text-white transition-colors" />
                    </div>
                    <h3 class="font-semibold text-gray-900 dark:text-white mb-1">
                        {{ __('View Progress') }}
                    </h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 flex-1">
                        {{ __('Check your points, badges, and level') }}
                    </p>
                    <div class="mt-3 flex items-center text-blue-600 dark:text-blue-400 text-sm font-medium opacity-0 group-hover:opacity-100 transition-opacity">
                        <span>{{ __('Start') }}</span>
                        <x-heroicon-m-arrow-right class="w-4 h-4 ml-1" />
                    </div>
                </div>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
