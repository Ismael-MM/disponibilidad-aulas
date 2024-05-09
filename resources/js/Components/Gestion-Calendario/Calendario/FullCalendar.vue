<script>
import { onBeforeMount } from "vue"
import FullCalendar from '@fullcalendar/vue3'

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