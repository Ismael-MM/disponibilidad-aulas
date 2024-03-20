<script setup>
import { ref, onBeforeMount, watch } from "vue"
import FullCalendar from "@/Components/Calendario/FullCalendar.vue";
import dayGridPlugin from '@fullcalendar/daygrid';
import multiMonthPlugin from '@fullcalendar/multimonth';
import esLocale from '@fullcalendar/core/locales/es';
import { useToast } from "vue-toastification"
import { compileScript } from "vue/compiler-sfc";


let filterSede = null;
let filterShift = 'm';

const filterPerShift = () => {

    if (filterShift == 'm') {
        filterShift = "t"
        useToast().info(
                "Turno cambiado a tarde"
            )
    } else {
        filterShift = "m"
        useToast().info(
                "Turno cambiado a mañana"
            )
    }
    newEvents();
}

const filterPerSede = () => {

}

const calendarOptions = ref({
    plugins: [dayGridPlugin, multiMonthPlugin],
    locale: esLocale,
    initialView: 'dayGridMonth',
    weekends: false,
    events: [],
    dayMaxEventRows: false,
    customButtons: {
        filtroTurno: {
            text: 'Turno',
            click: filterPerShift,
        }
    },
    headerToolbar: {
        left: 'dayGridMonth,multiMonthYear filtroTurno',
        center: 'title',
        right: 'today prev,next'
    },
});


const randomNumber = (min, max) => {
    return Math.floor(Math.random() * (max - min + 1)) + min;
}

const customColors = () => {
    let color = `#${randomNumber(350, 400)}`;

    console.log(color)

    return color;
}

const getReservasList = async () => {
    let reservasList = '';
    console.log(filterShift)
    await axios
        .get(route('dashboard.reservar.list', {
            turno: filterShift,
            sede: filterSede
        }))
        .then((response) => {
            const reserva = response.data.lists
            console.log(reserva)
            reservasList = reserva.map((reserva) => {
                return {
                    title: reserva.aula_id,
                    start: reserva.fecha_inicio,
                    end: reserva.fecha_fin,
                    color: customColors(),
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