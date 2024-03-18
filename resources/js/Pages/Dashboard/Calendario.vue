<script setup>
import {ref, onBeforeMount } from "vue"
import FullCalendar from "@/Components/Calendario/FullCalendar.vue";
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';

const calendarOptions = {
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
        { title: 'event 6', date: '2024-04-02' },
        { title: 'event 8', date: '2024-04-02' },
    ],
};


onBeforeMount(() => {
    
})
</script>

<template>
    <v-card elevation="6" class="ma-5" variant="outlined">
        <FullCalendar
        :options='calendarOptions'
        ></FullCalendar>
    </v-card>
</template>