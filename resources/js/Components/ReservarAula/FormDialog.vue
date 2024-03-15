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
const formattedDate = ref()
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
    console.log("fecha_inicio changed to:", newFechaInicio)
    console.log(cursosList.value.filter((e) => e.id == idCurso))
    console.log(idCurso)
    const curso = cursosList.value.filter((e) => e.id == idCurso)
    console.log(isNaN(idCurso))

    if (curso && isNaN(idCurso) == false && newFechaInicio) {
      totalDays(newFechaInicio, curso[0])
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

// funcion para calcular cuantos dias son necesarios para un curso
const totalDays = (fechainico, curso) => {
  console.log(fechainico)
  console.log(curso)
  if (fechainico != '' && (curso != '' || curso != undefined || curso != null)) {
    console.log("Totaldays horas", curso.horas)
    console.log("Totaldays horas diarias", curso.horas_diarias)
    console.log("toaldays dias", curso.horas / curso.horas_diarias)
    const diasSumar = curso.horas / curso.horas_diarias
    const fechaSinFormato = new Date(fechainico);

    const day = fechaSinFormato.getDate();
    const month = fechaSinFormato.getMonth() + 1;
    const year = fechaSinFormato.getFullYear();

    const fechaConFormato = year + "-" + month + "-" + day;

    console.log(fechaConFormato)

    calcularFechaFin(fechaConFormato, 'sumar', diasSumar, 'findes')
  }
}

function calcularFechaFin(fecha, operacion, dias, contador) {
  let date = fecha.split("-"),
    hoy = new Date(date[0], date[1], date[2]),
    diasSumar = dias,
    calculado = new Date(),
    dateResul = operacion == "sumar" ? hoy.getDate() + diasSumar : hoy.getDate() - diasSumar;
  calculado.setDate(dateResul);

  console.log(dateResul);

  console.log(calculado.getFullYear() + "-" + (calculado.getMonth() + 1) + "-" + calculado.getDate())
  const day = calculado.getDate().toString().padStart(2, '0');
  const month = (calculado.getMonth() + 1).toString().padStart(2, '0');
  const year = calculado.getFullYear();

  console.log((year + "-" + month + "-" + day))
  const fechaFin = (year + "-" + month + "-" + day);

  if (contador == 'findes') {
    console.log('findes entro')
    contarFindes(date, fechaFin, dias)
  } else if (contador == 'festivo') {

  } else {
    console.log(calculado)
    console.log(calculado.getDay() == 0)
    console.log(calculado.getDay() == 6)
    if (calculado.getDay() == 0) {
      calcularFechaFin(fecha, 'sumar', dias + 1)
      console.log(dias);
      console.log(calculado)
    }else if (calculado.getDay() == 6) {
      calcularFechaFin(fecha, 'sumar', dias + 2)
      console.log(dias);
      console.log(calculado)
    }else{
      formattedDate.value = fechaFin //muestra la fecha en el input
      formData.fecha_fin = fechaFin //guarda la fecha que se envia al controlador
    }
  }
}

function contarFindes(fechaInicio, fechaFin, diasSumados) {
  let inicio = new Date(fechaInicio); //Fecha inicial
  let fin = new Date(fechaFin); //Fecha final
  let timeDiff = Math.abs(fin.getTime() - inicio.getTime());
  let diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); //Días entre las dos fechas
  let cuentaFinde = 0; //Número de Sábados y Domingos

  console.log('inicio:', inicio)
  console.log('fin:', fin)
  for (let i = 0; i <= diffDays; i++) {
    //0 => Domingo - 6 => Sábado
    if (inicio.getDay() == 0 || inicio.getDay() == 6) {
      cuentaFinde++;
    }
    inicio.setDate(inicio.getDate() + 1);
  }

  console.log('findes:', cuentaFinde);
  console.log('dias sumados:', diasSumados);
  const sumaDias = diasSumados + cuentaFinde;
  console.log(sumaDias)
  const fechaInicioConFormato = (fechaInicio[0] + '-' + fechaInicio[1] + '-' + fechaInicio[2]);
  calcularFechaFin(fechaInicioConFormato, 'sumar', sumaDias);
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
                <v-text-field label="Fecha de fin*" type="date" v-model="formData.fecha_fin"
                  :value="formattedDate"></v-text-field>
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
