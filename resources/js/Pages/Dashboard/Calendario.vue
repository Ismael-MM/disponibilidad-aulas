<script>
import { onBeforeMount } from "vue"
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import esLocale from '@fullcalendar/core/locales/es';

export default {
    components: {
        FullCalendar // make the <FullCalendar> tag available
    },
    data: function () {
        return {
            calendarOptions: {
                plugins: [dayGridPlugin],
                locale: esLocale,
                initialView: 'dayGridMonth',
                weekends: false,
                events: [
                    { title: 'Meeting', start: new Date() },
                    {
                        title: 'Curso x aula 2',
                        start: '2024-03-05',
                        end: '2024-03-22',
                    },
                    { title: 'event 3', date: '2024-04-02' },
                ],
            }
        }
    }
}

onBeforeMount(() => { })
</script>

<template>
    <FullCalendar :options='calendarOptions' />
</template>