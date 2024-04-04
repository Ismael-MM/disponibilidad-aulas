<script setup>
import { ref, onBeforeMount, watch } from "vue"
import FullCalendar from "@/Components/Calendario/FullCalendar.vue";
import dayGridPlugin from '@fullcalendar/daygrid';
import multiMonthPlugin from '@fullcalendar/multimonth';
import esLocale from '@fullcalendar/core/locales/es';
import { useToast } from "vue-toastification"
import { compileScript } from "vue/compiler-sfc";




const openDialog = () => {
    dialog.value = true;
}

let rowCount = 0; //se usa para contar la line y asignar un color en customColor
const filterSede = ref(null);
const filterShift = ref('Mañana');
const sedeList = ref([]);
const dialog = ref(false);
const calendarOptions = ref({
    plugins: [dayGridPlugin, multiMonthPlugin],
    locale: esLocale,
    initialView: 'dayGridMonth',
    weekends: false,
    events: [],
    dayMaxEventRows: false,
    customButtons: {
        filtros: {
            text: 'Filtros',
            click: openDialog,
        }
    },
    headerToolbar: {
        left: 'dayGridMonth,multiMonthYear filtros',
        center: 'title',
        right: 'prev,today,next'
    },
});

const customColors = () => {
    let colorArray = ['#ade8f4','#90e0ef','#48cae4','#00b4d8','#0096c7','#0077b6','#023e8a','#03045E'];
    let color = '';

    switch (rowCount) {
        case 0:
            color = colorArray[0];
            rowCount++;
            break;
        case 1:
            color = colorArray[1];
            rowCount++;
            break;
        case 2:
            color = colorArray[2];
            rowCount++;
            break;
        case 3:
            color = colorArray[3];
            rowCount++;
            break;
        case 4:
            color = colorArray[4];
            rowCount++;
            break;
        case 5:
            color = colorArray[5];
            rowCount++;
            break;
        case 6:
            color = colorArray[6];
            rowCount++;
            break;
        case 7:
            color = colorArray[7];
            rowCount = 0;
            break;
    
        default:
            break;
    }
    return color;
}

const filterPerShift = () => {

    if (filterShift.value == 'Mañana' || filterShift.value == 'm') {
        filterShift.value = "m"
    } else {
        filterShift.value = "t"
    }
    rowCount = 0;
    newEvents();
    newFestivosEvents();
    calendarOptions.value.events = [];
    dialog.value = false;
    useToast().success(
        "Filtros aplicados correctamente"
    )
}



const getReservasList = async () => {
    let reservasList = '';
    await axios
        .get(route('dashboard.reservar.list', {
            turno: filterShift.value,
            sede: filterSede.value
        }))
        .then((response) => {
            const reserva = response.data.lists
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
    return reservasList;
}

const getSedesList = async () => {
    sedeList.value = []
    await axios
        .get(route('dashboard.sedes.exportExcel'))
        .then((response) => {
            const sede = response.data.itemsExcel
            sedeList.value = sede
        })
        .catch(() => {
            useToast().error(
                "Se ha producido un error al cargar los elementos del formulario. Intentalo de nuevo. Si el error persiste contacta con el administrador."
            )
        })
}

const getFestivosList = async () => {
  let festivosList = '';
  await axios
    .get(route('dashboard.festivo.list'))
    .then((response) => {
      const festivo = response.data.lists
            festivosList = festivo.map((festivo) => {
                return {
                    start: festivo.fecha,
                    end: festivo.fecha,
                    color: '#2E4057',
                    display: 'background',
                }
            });
    })
    .catch(() => {
      dialogState.value = false
      loading.value = false
      useToast().error(
        "Se ha producido un error al cargar los elementos del formulario. Intentalo de nuevo. Si el error persiste contacta con el administrador."
      )
    })
    return festivosList;
}

const newEvents = () => {
    getReservasList().then((reservasList) => {
        calendarOptions.value.events = reservasList;
        useToast().success(
        `Se han cargado ${reservasList.length} reservas`
        )
    });
};

const newFestivosEvents = (date) => {
    getFestivosList().then((festivosList) => {
        calendarOptions.value.events.push(...festivosList);
        console.log(festivosList)
        console.log(calendarOptions.value.events)
    });
    console.log("me ejecuto")
};

onBeforeMount(() => {
    newEvents();
    getSedesList();
    newFestivosEvents();
})
</script>

<template>
    <v-card elevation="6" class="ma-5" variant="outlined">
        <FullCalendar :options='calendarOptions'></FullCalendar>
    </v-card>
    <v-dialog v-model="dialog" width="1024">
        <v-card prepend-icon="mdi-filter-cog-outline">
            <v-card-title>
                <span class="text-h5">Filtros</span>
            </v-card-title>
            <v-card-text>
                <v-container>
                    <v-form>
                        <v-row>
                            <v-col cols="12" sm="6">
                                <v-select label="Turno" :items="['Mañana', 'Tarde',]" v-model="filterShift"></v-select>
                            </v-col>
                            <v-col cols="12" sm="6">
                                <v-autocomplete label="Sedes" :items="[...sedeList]" item-title="nombre"
                                    item-value="nombre" v-model="filterSede">
                                </v-autocomplete>
                            </v-col>
                        </v-row>
                    </v-form>
                </v-container>
            </v-card-text>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="blue-darken-1" variant="text" @click="dialog = false">
                    Cerrar
                </v-btn>
                <v-btn color="blue-darken-1" variant="text" @click="filterPerShift()">
                    Filtrar
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>