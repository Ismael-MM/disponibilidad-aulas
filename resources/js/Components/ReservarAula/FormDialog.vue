<script setup>
import { useForm } from "@inertiajs/vue3"
import { computed, watch, ref } from "vue"
import { useToast } from "vue-toastification"
import {
  ruleRequired,
  ruleMaxLength,
} from "@/Utils/rules"
import { sexoItems } from "@/Utils/arrays"

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
      .get(route('dashboard.cursos.exportExcel'))
      .then((response) => {
        const curso = response.data.itemsExcel
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
      .get(route('dashboard.aulas.exportExcel'))
      .then((response) => {
        const aula = response.data.itemsExcel
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
</script>

<template>
  <v-dialog v-model="dialogState" width="1024">
    <v-card>
      <v-card-title>
        <span class="text-h5"
          >{{
            props.type == "create"
              ? "Crear"
              : props.type == "edit"
              ? "Editar"
              : ""
          }}
          Cursos</span
        >
      </v-card-title>

      <v-card-text>
        <v-container>
          <v-form v-model="form" @submit.prevent="submit">
            <v-row>
              <v-col cols="12" sm="6">
                <v-autocomplete
                  label="Aula*"
                  :rules="[ruleRequired]"
                  :items="[...aulasList]"
                  item-title="nombre"
                  item-value="id"
                  v-model="formData.aula_id"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" sm="6">
                <v-autocomplete
                  label="Curso*"
                  :rules="[ruleRequired]"
                  :items="[...cursosList]"
                  item-title="titulo"
                  item-value="id"
                  v-model="formData.curso_id"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  label="Fecha de inicio*"
                  :rules="[ruleRequired]"
                  type="date"
                  v-model="formData.fecha_inicio"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  label="Fecha de fin*"
                  :rules="[ruleRequired]"
                  type="date"
                  v-model="formData.fecha_fin"
                ></v-text-field>
              </v-col>
            </v-row>
          </v-form>
        </v-container>
      </v-card-text>

      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="blue-darken-1"
          variant="text"
          @click="dialogState = false"
        >
          Cerrar
        </v-btn>
        <v-btn
          color="blue-darken-1"
          :disabled="!form"
          variant="text"
          @click="submit"
        >
          Guardar
        </v-btn>
        <v-btn
          color="blue-darken-1"
          variant="text"
          @click="console.log(formData)"
        >
          log
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
