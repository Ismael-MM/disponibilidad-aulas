<script setup>
import { useForm } from "@inertiajs/vue3"
import { computed, watch, ref } from "vue"
import {
  ruleRequired,
  ruleMaxLength,
  ruleLessThan,
  ruleGreaterThan,
} from "@/Utils/rules"
import { sexoItems } from "@/Utils/arrays"

const props = defineProps(["show", "item", "type", "endPoint"])
const emit = defineEmits(["closeDialog", "reloadItems"])

const dialogState = computed({
  get: () => props.show,
  set: (value) => {
    emit("closeDialog", value)
  },
})

const form = ref(false)

const formData = useForm({
  titulo: "",
  turno: "",
  horas: "",
  horas_diarias: "",
})

watch(dialogState, (value) => {
  if (value) {
    if (props.type === "edit") {
      Object.assign(formData, props.item)
    } else if (props.type === "create") {
      formData.titulo = ""
      formData.turno = ""
      formData.horas = ""
      formData.horas_diarias = ""
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
                <v-text-field
                  label="Titulo*"
                  :rules="[ruleRequired, (v) => ruleMaxLength(v, 191)]"
                  v-model="formData.titulo"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-select
                  label="Turno*"
                  :items="['M','T']"
                  v-model="formData.turno"
                ></v-select>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  label="Horas Totales*"
                  :rules="[ruleRequired, (v) => ruleLessThan(v, 3001)]"
                  type="number"
                  v-model="formData.horas"
                ></v-text-field>
              </v-col>
              <v-col cols="12" sm="6">
                <v-text-field
                  label="Horas Diarias*"
                  :rules="[ruleRequired, (v) => ruleLessThan(v,11)]"
                  type="number"
                  v-model="formData.horas_diarias"
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
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>
