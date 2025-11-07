<x-filament-panels::page>
    @if (method_exists($this, 'filtersForm'))
        {{ $this->filtersForm }}
    @endif

    <div class="space-y-6">
        <!-- Widgets -->
        <x-filament-widgets::widgets
            :columns="$this->getColumns()"
            :data="
                [
                    ...(property_exists($this, 'filters') ? ['filters' => $this->filters] : []),
                    ...($this->getWidgetData()),
                ]
            "
            :widgets="$this->getVisibleWidgets()"
        />
    </div>

    <style>
        /* Custom styles for dashboard */
        .fi-section {
            @apply shadow-sm hover:shadow-md transition-shadow;
        }

        .fi-stats-card {
            @apply rounded-xl;
        }

        /* Gradient backgrounds */
        .gradient-purple {
            background: linear-gradient(135deg, #8B5CF6 0%, #7C3AED 100%);
        }

        .gradient-yellow-white {
            background: linear-gradient(135deg, #FEF3C7 0%, #FFFFFF 100%);
        }

        .gradient-blue-white {
            background: linear-gradient(135deg, #DBEAFE 0%, #FFFFFF 100%);
        }
    </style>
</x-filament-panels::page>
