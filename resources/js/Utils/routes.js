import { computed } from "vue"

/*
 * Parent routes will have "path" parameters instead or "route" ones
 * "routes" will be used in their childs
 */

export const routesArray = [
  {
    value: "Usuario",
    icon: "mdi-account-circle",
  },
  {
    value: "Calendario",
    route: "dashboard.calendario",
    icon: "mdi-calendar-month",
  },
  {
    value: "Gestión",
    icon: "mdi-animation",
    path: "Gestion",
    childs: [
      {
        value: "Cursos",
        route: "dashboard.cursos",
        icon: "mdi-school",
      },
      {
        value: "Aulas",
        route: "dashboard.aulas",
        icon: "mdi-google-classroom",
      },
      {
        value: "Sedes",
        route: "dashboard.sedes",
        icon: "mdi-home-group",
      },
    ],
  },
  {
    value: "Gestión calendario",
    icon: "mdi-calendar-month",
    path: "Calendario",
    childs: [
      {
        value: "Reservar aula",
        route: "dashboard.reservar",
        icon: "mdi-calendar-clock",
      },
      {
        value: "Festivos",
        route: "dashboard.festivo",
        icon: "mdi-calendar-multiselect",
      },
    ],
  },
  {
    value: "Cerrar sesión",
    icon: "mdi-logout-variant",
    route: "logout",
  },
  // Route with childs example:
  /*
  {
    value: "title",
    icon: "icon",
    path: "relative-path",
    childs: [
      {
        value: "title",
        route: "route name",
        icon: "icon",
      },
      {
        value: "title",
        route: "route name",
        icon: "icon",
      },
    ],
  },
  */
]

export const routes = computed({
  get() {
    return routesArray
  },
  set({
    newValue,
    element = 1,
    key = "value",
    child = false,
    childElement = 1,
  }) {
    let handler = routesArray

    if (child) {
      handler[element - 1].childs[childElement - 1][key] = newValue
    } else {
      handler[element - 1][key] = newValue
    }

    return handler
  },
})
