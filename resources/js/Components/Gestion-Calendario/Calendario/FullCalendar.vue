<script>
import { onBeforeMount } from "vue"
import FullCalendar from '@fullcalendar/vue3'
import dayGridPlugin from '@fullcalendar/daygrid'
import esLocale from '@fullcalendar/core/locales/es';

export default {
    components: {
        FullCalendar // make the <FullCalendar> tag available
    },
};


onBeforeMount(() => { })
</script>

<template>
    <FullCalendar/>
</template>