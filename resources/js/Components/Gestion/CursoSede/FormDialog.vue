<script setup>
import { useForm } from "@inertiajs/vue3"
import { computed, watch, ref } from "vue"
import { useToast } from "vue-toastification"
import {
  ruleRequired,
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

const sedeList = ref([])
const cursoList = ref([])
const form = ref(false)
const formData = useForm({
  sede_id: "",
  curso_id: "",
})

watch(dialogState, (value) => {
  if (value) {
    loading.value = true
    getSedesList()
    getCursosList()
    
    if (props.type === "edit") {
      Object.assign(formData, props.item)
    } else if (props.type === "create") {
      formData.nombre = ""
      formData.sede_id = ""
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

const getSedesList = async () => {
  sedeList.value = []
     await axios
      .get(route('dashboard.sedes.exportExcel'))
      .then((response) => {
        const sede = response.data.itemsExcel
        sedeList.value = sede
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

const getCursosList = async () => {
    cursoList.value = []
     await axios
      .get(route('dashboard.cursos.list'))
      .then((response) => {
        const curso = response.data.lists
        cursoList.value = curso
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
                  label="Cursos*"
                  :rules="[ruleRequired]"
                  :items="[...cursoList]"
                  item-title="tituloturno"
                  item-value="id"
                  v-model="formData.curso_id"
                ></v-autocomplete>
              </v-col>
              <v-col cols="12" sm="6">
                <v-autocomplete
                  label="Sedes*"
                  :rules="[ruleRequired]"
                  :items="[...sedeList]"
                  item-title="nombre"
                  item-value="id"
                  v-model="formData.sede_id"
                ></v-autocomplete>
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
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
