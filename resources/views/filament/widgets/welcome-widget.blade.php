@php
    $data = $this->getData();
@endphp

<x-filament-widgets::widget>
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl p-8 text-white shadow-xl relative overflow-hidden">
        <!-- Decorative background elements -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-white opacity-5 rounded-full -mr-32 -mt-32"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-white opacity-5 rounded-full -ml-24 -mb-24"></div>

        <div class="relative z-10">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-3xl font-bold mb-2">
                        Welcome back, {{ $data['name'] }}! 👋
                    </h2>
                    <p class="text-purple-100 text-lg">
                        Ready to continue your learning journey? Let's make today count!
                    </p>
                </div>

                <!-- User level badge -->
                <div class="flex flex-col items-center bg-white/20 backdrop-blur-sm rounded-xl px-6 py-4 border border-white/30">
                    <span class="text-sm font-medium text-purple-100 uppercase tracking-wide">Level</span>
                    <span class="text-4xl font-bold mt-1">{{ $data['level'] }}</span>
                    <div class="mt-2 flex items-center gap-1">
                        @for($i = 0; $i < min($data['badges_count'], 3); $i++)
                            <div class="w-2 h-2 bg-yellow-400 rounded-full"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <!-- Quick stats grid -->
            <div class="grid grid-cols-4 gap-4 mt-8">
                <!-- Total Points -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-all">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-yellow-400 rounded-lg flex items-center justify-center">
                            <x-heroicon-s-star class="w-5 h-5 text-purple-900" />
                        </div>
                        <span class="text-sm font-medium text-purple-100">Points</span>
                    </div>
                    <p class="text-2xl font-bold">{{ number_format($data['total_points']) }}</p>
                </div>

                <!-- Subjects -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-all">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-green-400 rounded-lg flex items-center justify-center">
                            <x-heroicon-s-book-open class="w-5 h-5 text-green-900" />
                        </div>
                        <span class="text-sm font-medium text-purple-100">Subjects</span>
                    </div>
                    <p class="text-2xl font-bold">{{ $data['subjects_count'] }}</p>
                </div>

                <!-- Exercises Completed -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-all">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-blue-400 rounded-lg flex items-center justify-center">
                            <x-heroicon-s-check-circle class="w-5 h-5 text-blue-900" />
                        </div>
                        <span class="text-sm font-medium text-purple-100">Completed</span>
                    </div>
                    <p class="text-2xl font-bold">{{ number_format($data['exercises_completed']) }}</p>
                </div>

                <!-- Study Streak -->
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-4 border border-white/20 hover:bg-white/15 transition-all">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="w-8 h-8 bg-orange-400 rounded-lg flex items-center justify-center">
                            <x-heroicon-s-fire class="w-5 h-5 text-orange-900" />
                        </div>
                        <span class="text-sm font-medium text-purple-100">Streak</span>
                    </div>
                    <p class="text-2xl font-bold">{{ $data['study_streak'] }} days</p>
                </div>
            </div>

            <!-- CTA Button -->
            <div class="mt-6">
                <a href="{{ route('filament.admin.resources.exercises.index') }}"
                   class="inline-flex items-center gap-2 bg-white text-purple-600 font-semibold px-6 py-3 rounded-xl hover:bg-purple-50 transition-all shadow-lg hover:shadow-xl">
                    <span>Start Learning Now</span>
                    <x-heroicon-m-arrow-right class="w-5 h-5" />
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
