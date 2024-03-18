<script setup>
import { useForm } from "@inertiajs/vue3"
import { computed, watch, ref } from "vue"
import { useToast } from "vue-toastification"
import {
  ruleRequired,
  ruleMaxLength,
} from "@/Utils/rules"

const props = defineProps(["show", "item", "type", "endPoint"])
const emit = defineEmits(["closeDialog", "reloadItems"])


const loading = ref(false)
const dialogState = computed({
  get: () => props.show,
  set: (value) => {
    emit("closeDialog", value)
  },
})

const cursosList = ref([])
const aulasList = ref([])
const festivosList = ref([])
const form = ref(false)
const formData = useForm({
  aula_id: "",
  curso_id: "",
  fecha_inicio: "",
  fecha_fin: "",
})

watch(dialogState, (value) => {
  if (value) {
    loading.value = true
    getCursosList()
    getAulasList()

    if (props.type === "edit") {
      Object.assign(formData, props.item)
    } else if (props.type === "create") {
      formData.aula_id = ""
      formData.curso_id = ""
      formData.fecha_fin = ""
      formData.fecha_inicio = ""
    }
  } else {
    emit("reloadItems")
  }
})

watch(
  () => [formData.fecha_inicio, formData.curso_id],
  ([newFechaInicio, idCurso]) => {
    const curso = cursosList.value.filter((e) => e.id == idCurso)

    if (curso && isNaN(idCurso) == false && newFechaInicio) {
      getFestivosList(newFechaInicio, curso[0])
    }

  }
)

const submit = () => {
  if (props.type === "edit") {
    formData.put(`${props.endPoint}/${props.item.id}`, {
      only: ["tableData", "flash", "errors"],
      onSuccess: () => {
        dialogState.value = false
      },
    })
  } else if (props.type === "create") {
    formData.post(props.endPoint, {
      only: ["tableData", "flash", "errors"],
      onSuccess: () => {
        dialogState.value = false
      },
    })
  }
}

const getCursosList = async () => {
  cursosList.value = []
  await axios
    .get(route('dashboard.cursos.list'))
    .then((response) => {
      const curso = response.data.lists
      cursosList.value = curso
      loading.value = false
    })
    .catch(() => {
      dialogState.value = false
      loading.value = false
      useToast().error(
        "Se ha producido un error al cargar los elementos del formulario. Intentalo de nuevo. Si el error persiste contacta con el administrador."
      )
    })
}

const getAulasList = async () => {
  aulasList.value = []
  await axios
    .get(route('dashboard.aulas.list'))
    .then((response) => {
      const aula = response.data.lists
      aulasList.value = aula
      loading.value = false
    })
    .catch(() => {
      dialogState.value = false
      loading.value = false
      useToast().error(
        "Se ha producido un error al cargar los elementos del formulario. Intentalo de nuevo. Si el error persiste contacta con el administrador."
      )
    })
}

const getFestivosList = async (fechainico, curso) => {
  let festivosList = []
  await axios
    .get(route('dashboard.festivo.list'))
    .then((response) => {
      const festivo = response.data.lists
      festivosList.value = festivo.map((festivo) => [festivo.dia, festivo.mes])
      loading.value = false
      totalDays(fechainico, curso, festivosList)
    })
    .catch(() => {
      dialogState.value = false
      loading.value = false
      useToast().error(
        "Se ha producido un error al cargar los elementos del formulario. Intentalo de nuevo. Si el error persiste contacta con el administrador."
      )
    })
}


// funcion para calcular cuantos dias son necesarios para un curso
const totalDays = (fechainico, curso, festivosList) => {
  console.log(fechainico)
  console.log(curso)
  if (fechainico != '' && (curso != '' || curso != undefined || curso != null)) {
    const diasSumar = curso.horas / curso.horas_diarias
    const fechaSinFormato = new Date(fechainico);

    const day = fechaSinFormato.getDate() - 1 ; //  El dia actual que estamos en el mes
    const month = fechaSinFormato.getMonth() + 1; // El mes actual
    const year = fechaSinFormato.getFullYear();

    const fechaConFormato = year + "-" + month + "-" + day;

    formData.fecha_fin =  getSinFestivosNiFinDeSemana(fechaConFormato, diasSumar, festivosList)
  }
}

const getSinFestivosNiFinDeSemana = (fechaInico, diasAdd, festivosList) => {

    let arrFecha = fechaInico.split('-');
    let fecha = new Date(arrFecha[0], arrFecha[1] - 1, arrFecha[2]);
    let festivos = festivosList.value;

    console.log(festivos)



    for (let i = 0; i < diasAdd; i++) {
        let diaInvalido = false;
        fecha.setDate(fecha.getDate() + 1); // Sumamos de dia en dia
        for (let j = 0; j < festivos.length; j++) { // Verificamos si el dia + 1 es festivo
            let mesDia = festivos[j];
            if (fecha.getMonth() + 1 == mesDia[1] && fecha.getDate() == mesDia[0]) {
                console.log(fecha.getDate() + ' es dia festivo (Sumamos un dia)');
                diaInvalido = true;
                break;
            }
        }
        if (fecha.getDay() == 0 || fecha.getDay() == 6) { // Verificamos si es sábado o domingo
            console.log(fecha.getDate() + ' es sábado o domingo (Sumamos un dia)');
            diaInvalido = true;
        }
        if (diaInvalido)
            diasAdd++; // Si es fin de semana o festivo le sumamos un dia
    }
    return fecha.getFullYear() + '-' + (fecha.getMonth() + 1).toString().padStart(2, '0') + '-' + fecha.getDate().toString().padStart(2, '0');
}

</script>


<template>
  <v-dialog v-model="dialogState" width="1024">
    <v-card>
      <v-card-title>
        <span class="text-h5">{{
    props.type == "create"
      ? "Crear"
      : props.type == "edit"
        ? "Editar"
        : ""
  }}
          Reserva</span>
      </v-card-title>

      <v-card-text>
        <v-container>
          <v-form v-model="form" @submit.prevent="submit">
            <v-row>
              <v-col cols="12" sm="6">
                <v-autocomplete label="Aula*" :rules="[ruleRequired]" :items="[...aulasList]" item-title="aulasede"
                  item-value="id" v-model="formData.aula_id"></v-autocomplete>
              </v-col>
              <v-col cols="12" sm="6">
                <v-autocomplete label="Curso*" :rules="[ruleRequired]" :items="[...cursosList]" item-title="tituloturno"
                  item-value="id" v-model="formData.curso_id"></v-autocomplete>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field label="Fecha de inicio*" :rules="[ruleRequired]" type="date"
                  v-model="formData.fecha_inicio"></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field label="Fecha de fin*" type="date" 
                v-model="formData.fecha_fin" ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-container>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn color="blue-darken-1" variant="text" @click="dialogState = false">
          Cerrar
        </v-btn>
        <v-btn color="blue-darken-1" :disabled="!form" variant="text" @click="submit">
          Guardar
        </v-btn>
        <v-btn color="blue-darken-1" variant="text" @click="console.log(cursosList)">
          log
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
