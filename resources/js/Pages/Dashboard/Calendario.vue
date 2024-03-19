<script setup>
import { ref, onBeforeMount, watch } from "vue"
import FullCalendar from "@/Components/Calendario/FullCalendar.vue";
import dayGridPlugin from '@fullcalendar/daygrid';
import esLocale from '@fullcalendar/core/locales/es';


const calendarOptions = ref({ 
    plugins: [dayGridPlugin],
    locale: esLocale,
    initialView: 'dayGridMonth',
    weekends: false,
    events:[]
});

watch(calendarOptions.value.events, (newVal, oldVal) => {
    if (newVal.length !== oldVal.length) {
        newEvents();
    }
});

const getReservasList = async () => {
    let reservasList = '';
    await axios
        .get(route('dashboard.reservar.list'))
        .then((response) => {
            const reserva = response.data.lists
            console.log(reserva)
            reservasList = reserva.map((reserva) => {
                return {
                    title: reserva.aula_id,
                    start: reserva.fecha_inicio,
                    end: reserva.fecha_fin,
                }
        });
        })
        .catch(() => {
            useToast().error(
                "Se ha producido un error al cargar los elementos del formulario. Intentalo de nuevo. Si el error persiste contacta con el administrador."
            )
        })
        console.log(reservasList)
    return reservasList;
}

const newEvents = () => {
    getReservasList().then((reservasList) => {
        calendarOptions.value.events = reservasList;
        console.log(reservasList)
    });
};

onBeforeMount(() => {
    newEvents();
})
</script>

<template>
    <v-card elevation="6" class="ma-5" variant="outlined">
        <FullCalendar :options='calendarOptions'></FullCalendar>
    </v-card>
</template>