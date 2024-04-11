# Proyecto de Disponibilidad de Aulas 🏫

## Descripción 📝
Este proyecto tiene como objetivo gestionar la disponibilidad de aulas en una institución educativa. Los usuarios podrán consultar la disponibilidad de aulas en tiempo real y reservarlas según sus necesidades.

## Tecnologías Utilizadas 💻
### Backend (Laravel) 🚀
- **Laravel**: Framework de backend en PHP que nos permite crear una API robusta para gestionar los datos y la lógica del sistema.
- **Inertia.js**: Biblioteca que combina las ventajas de las SPAs (Single-Page Applications) con la simplicidad de las aplicaciones tradicionales de servidor. Permite utilizar Vue.js sin la necesidad de crear una API separada.

### Frontend (Inertia.js + Vue.js + Vuetify) 🎨
- **Vue.js**: Framework de JavaScript para construir interfaces de usuario interactivas y dinámicas.
- **Inertia.js**: Integración con Laravel mediante Inertia.js para obtener datos en tiempo real.
- **Vuetify**: Framework de diseño basado en Material Design para crear componentes visuales atractivos y funcionales.


## Funcionalidades Principales ✨
1. **Visualización de Aulas ocupadas**: Los usuarios pueden ver la lista de aulas ocupadas horarios.
2. **Reserva de Aulas**: Los usuarios registrados pueden reservar un aula específica para un período determinado.
3. **Administración de Aulas**: Los usuarios pueden agregar, editar o eliminar aulas desde el panel de administración.

## Estructura del Proyecto 🏗️
- **Backend (Laravel)**:
    - Rutas WEB para consultar la disponibilidad de aulas y gestionar reservas.
    - Controladores para manejar las solicitudes relacionadas con las aulas y las reservas.
    - Modelos para representar las aulas y las reservas en la base de datos.
    - Migraciones y seeders para crear y poblar las tablas.
- **Frontend (Vue)**:
    - Componentes Vue para mostrar la lista de aulas y permitir la reserva.
    - Integración con la API de Laravel para obtener datos en tiempo real.
    - Diseño y estilos utilizando Vue y CSS.

## Instalación 🛠️
1. Clona este repositorio en tu máquina local.
2. Configura el .env y genera una clave de aplicación
2. Ejecuta las migraciones y los seeders para crear la base de datos con datos de ejemplo.
3. Inicia el servidor de desarrollo tanto para Laravel como para Vue.
> [!NOTE]
> Para generar la clave usa php artisan key:generate.
>
> Usa npm run dev para iniciar el servidor de desarrollo.
