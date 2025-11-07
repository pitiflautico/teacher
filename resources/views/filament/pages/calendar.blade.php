<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white rounded-lg shadow p-6 dark:bg-gray-800">
            <div id="calendar"></div>
        </div>
    </div>

    @push('styles')
    <style>
        #calendar {
            min-height: 600px;
        }
        .fc {
            font-family: inherit;
        }
        .fc-button {
            background-color: rgb(59, 130, 246) !important;
            border-color: rgb(59, 130, 246) !important;
        }
        .fc-button:hover {
            background-color: rgb(37, 99, 235) !important;
        }
        .fc-button-active {
            background-color: rgb(29, 78, 216) !important;
        }
    </style>
    @endpush

    @push('scripts')
    <script type="module">
        import { Calendar } from '@fullcalendar/core';
        import dayGridPlugin from '@fullcalendar/daygrid';
        import timeGridPlugin from '@fullcalendar/timegrid';
        import interactionPlugin from '@fullcalendar/interaction';

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var events = {!! $events !!};

            var calendar = new Calendar(calendarEl, {
                plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: events,
                editable: true,
                selectable: true,
                selectMirror: true,
                dayMaxEvents: true,
                weekends: true,
                eventClick: function(info) {
                    alert('Event: ' + info.event.title + '\n' +
                          'Type: ' + info.event.extendedProps.type + '\n' +
                          (info.event.extendedProps.location ? 'Location: ' + info.event.extendedProps.location : ''));
                },
                select: function(info) {
                    window.location.href = '/admin/calendar-events/create?start=' + 
                        info.startStr + '&end=' + info.endStr;
                }
            });

            calendar.render();
        });
    </script>
    @endpush
</x-filament-panels::page>
