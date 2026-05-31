# Documentacion del Proyecto: Consultorio Dental

## 1. Resumen general

Este proyecto es un sistema web para administrar un consultorio dental. Esta construido con Laravel, Blade, Bootstrap y PostgreSQL.

El sistema permite gestionar:

- Usuarios y autenticacion.
- Pacientes.
- Dentistas.
- Citas.
- Expedientes clinicos.
- Tratamientos.
- Recetas.
- Inventario.
- Documentos adjuntos en expedientes clinicos.
- Configuracion de cuenta y cambio de contrasena.
- Alertas simples mediante jobs.
- API REST protegida con Sanctum.

La aplicacion es una app monolitica Laravel: el backend, las vistas web, la API y la logica de base de datos viven dentro del mismo proyecto.

## 2. Tecnologias utilizadas

- PHP 8.3 o superior.
- Laravel 13.
- Blade como motor de plantillas.
- Bootstrap 5 desde CDN para estilos.
- PostgreSQL como base de datos.
- Docker Compose para levantar bases PostgreSQL.
- Laravel Sanctum para tokens de API.
- Eloquent ORM para consultas y relaciones.
- Vite para compilar assets frontend.
- Jobs de Laravel para procesos en cola.

## 3. Estructura principal del proyecto

```text
app/
  Http/Controllers/     Controladores HTTP y API
  Jobs/                 Tareas en cola
  Models/               Modelos Eloquent

database/
  migrations/           Definicion de tablas
  seeders/              Datos iniciales
  factories/            Generacion de datos de prueba

resources/views/
  layouts/              Layout base
  auth/                 Login
  pacientes/            Vistas del modulo Pacientes
  citas/                Vistas del modulo Citas
  dentistas/            Vistas del modulo Dentistas
  expedientes/          Vistas del modulo Expedientes
  tratamientos/         Vistas del modulo Tratamientos
  recetas/              Vistas del modulo Recetas
  inventario/           Vistas del modulo Inventario
  vendor/pagination/    Paginacion Bootstrap personalizada

routes/
  web.php               Rutas web Blade
  api.php               Rutas API JSON

config/
  database.php          Configuracion de base de datos
  auth.php              Configuracion de autenticacion
  sanctum.php           Configuracion de Sanctum

docker-compose.yml      Servicios PostgreSQL
comandos.md             Comandos de servidor/despliegue
consultorio_backup.sql  Respaldo de base de datos
```

## 4. Arquitectura

El proyecto usa MVC:

```text
Request del usuario
    -> routes/web.php o routes/api.php
    -> Controller o closure de ruta
    -> Model Eloquent
    -> PostgreSQL
    -> View Blade o JSON
    -> Response al usuario
```

### Capas

**Rutas**

Definen las URLs disponibles y conectan cada URL con una funcion, closure o controlador.

**Controladores**

Contienen logica de validacion, consulta, creacion, actualizacion y eliminacion.

**Modelos**

Representan tablas de base de datos y relaciones entre entidades.

**Vistas**

Renderizan HTML usando Blade y Bootstrap.

**Migraciones**

Crean las tablas y relaciones en PostgreSQL.

## 5. Base de datos

El sistema usa PostgreSQL.

En `.env`:

```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5434
DB_DATABASE=consultorio_dental
DB_USERNAME=admin
DB_PASSWORD=admin123
```

En `docker-compose.yml` hay dos servicios:

```text
postgres_consultorio_1 -> puerto local 5434
postgres_consultorio_2 -> puerto local 5433
```

El contenedor principal es:

```text
postgres_consultorio_1
```

El respaldo SQL se restaura con:

```bash
cat consultorio_backup.sql | docker exec -i postgres_consultorio_1 psql -U admin -d consultorio_dental
```

## 6. Tablas principales

### users

Guarda usuarios del sistema.

Campos importantes:

- `id`
- `name`
- `email`
- `password`
- `rol`
- `remember_token`
- `created_at`
- `updated_at`

El campo `rol` se agrega en la migracion:

```text
2026_05_30_163057_add_rol_to_users_table.php
```

Roles contemplados:

- `administrador`
- `dentista`
- `recepcionista`
- `paciente`

### pacientes

Guarda informacion personal y medica basica del paciente.

Campos:

- `id`
- `nombre`
- `apellido_paterno`
- `apellido_materno`
- `telefono`
- `correo`
- `fecha_nacimiento`
- `curp`
- `tipo_sangre`
- `alergias`
- `antecedentes_medicos`
- timestamps

### dentistas

Guarda informacion del personal dental.

Campos:

- `id`
- `nombre`
- `apellido_paterno`
- `apellido_materno`
- `especialidad`
- `cedula_profesional`
- `telefono`
- `correo`
- `horario_inicio`
- `horario_fin`
- `consultorio`
- timestamps

### citas

Guarda citas dentales.

Campos:

- `id`
- `paciente_id`
- `dentista_id`
- `fecha`
- `hora_inicio`
- `hora_fin`
- `duracion_minutos`
- `motivo`
- `estado`
- timestamps

Estados:

- `pendiente`
- `confirmada`
- `cancelada`
- `finalizada`

Relaciones:

- Una cita pertenece a un paciente.
- Una cita pertenece a un dentista.

### expedientes

Guarda informacion clinica del paciente.

Campos:

- `id`
- `paciente_id`
- `diagnostico`
- `observaciones`
- `procedimientos_realizados`
- `evolucion_tratamiento`
- timestamps

Regla:

- `paciente_id` es unico, por lo que un paciente tiene un expediente.

### tratamientos

Guarda tratamientos realizados o planeados.

Campos:

- `id`
- `paciente_id`
- `dentista_id`
- `expediente_id`
- `cita_id`
- `nombre`
- `descripcion`
- `costo`
- `estado`
- `fecha_inicio`
- `fecha_fin`
- timestamps

Estados:

- `pendiente`
- `en_proceso`
- `finalizado`
- `cancelado`

### recetas

Guarda recetas medicas.

Campos:

- `id`
- `paciente_id`
- `dentista_id`
- `tratamiento_id`
- `medicamento`
- `dosis`
- `frecuencia`
- `duracion`
- `indicaciones`
- `fecha_emision`
- timestamps

### inventarios

Guarda productos, materiales e insumos.

Campos:

- `id`
- `nombre`
- `categoria`
- `cantidad`
- `stock_minimo`
- `fecha_caducidad`
- `proveedor`
- `precio_unitario`
- timestamps

## 7. Modelos Eloquent

### App\Models\User

Archivo:

```text
app/Models/User.php
```

Representa usuarios del sistema.

Caracteristicas:

- Extiende `Authenticatable`.
- Usa `HasApiTokens` para Sanctum.
- Usa `HasFactory`.
- Usa `Notifiable`.
- Campos asignables con atributo `#[Fillable]`:
  - `name`
  - `email`
  - `password`
  - `rol`
- Campos ocultos:
  - `password`
  - `remember_token`
- Casts:
  - `email_verified_at` como datetime.
  - `password` como hashed.

### App\Models\Paciente

Archivo:

```text
app/Models/Paciente.php
```

Representa la tabla `pacientes`.

Campos fillable:

- `nombre`
- `apellido_paterno`
- `apellido_materno`
- `telefono`
- `correo`
- `fecha_nacimiento`
- `curp`
- `tipo_sangre`
- `alergias`
- `antecedentes_medicos`

Relaciones:

- `citas()`: un paciente tiene muchas citas.
- `expediente()`: un paciente tiene un expediente.

### App\Models\Dentista

Archivo:

```text
app/Models/Dentista.php
```

Representa la tabla `dentistas`.

Campos fillable:

- `nombre`
- `apellido_paterno`
- `apellido_materno`
- `especialidad`
- `cedula_profesional`
- `telefono`
- `correo`
- `horario_inicio`
- `horario_fin`
- `consultorio`

Relaciones:

- `citas()`: un dentista tiene muchas citas.

### App\Models\Cita

Archivo:

```text
app/Models/Cita.php
```

Representa la tabla `citas`.

Campos fillable:

- `paciente_id`
- `dentista_id`
- `fecha`
- `hora_inicio`
- `hora_fin`
- `duracion_minutos`
- `motivo`
- `estado`

Relaciones:

- `paciente()`: una cita pertenece a un paciente.
- `dentista()`: una cita pertenece a un dentista.

### App\Models\Expediente

Archivo:

```text
app/Models/Expediente.php
```

Representa la tabla `expedientes`.

Campos fillable:

- `paciente_id`
- `diagnostico`
- `observaciones`
- `procedimientos_realizados`
- `evolucion_tratamiento`

Relaciones:

- `paciente()`: un expediente pertenece a un paciente.
- `documentos()`: un expediente tiene muchos documentos adjuntos.

### App\Models\ExpedienteDocumento

Archivo:

```text
app/Models/ExpedienteDocumento.php
```

Representa los archivos adjuntos de un expediente clinico.

Campos fillable:

- `expediente_id`
- `tipo`
- `nombre_original`
- `ruta`
- `mime_type`
- `tamano`

Relaciones:

- `expediente()`: un documento pertenece a un expediente.

### App\Models\Tratamiento

Archivo:

```text
app/Models/Tratamiento.php
```

Representa la tabla `tratamientos`.

Campos fillable:

- `paciente_id`
- `dentista_id`
- `expediente_id`
- `cita_id`
- `nombre`
- `descripcion`
- `costo`
- `estado`
- `fecha_inicio`
- `fecha_fin`

Relaciones:

- `paciente()`
- `dentista()`
- `expediente()`
- `cita()`

### App\Models\Receta

Archivo:

```text
app/Models/Receta.php
```

Representa la tabla `recetas`.

Campos fillable:

- `paciente_id`
- `dentista_id`
- `tratamiento_id`
- `medicamento`
- `dosis`
- `frecuencia`
- `duracion`
- `indicaciones`
- `fecha_emision`

Relaciones:

- `paciente()`
- `dentista()`
- `tratamiento()`

### App\Models\Inventario

Archivo:

```text
app/Models/Inventario.php
```

Representa la tabla `inventarios`.

Campos fillable:

- `nombre`
- `categoria`
- `cantidad`
- `stock_minimo`
- `fecha_caducidad`
- `proveedor`
- `precio_unitario`

## 8. Controladores

### Controller

Archivo:

```text
app/Http/Controllers/Controller.php
```

Clase base abstracta para los controladores.

### AuthController

Archivo:

```text
app/Http/Controllers/AuthController.php
```

Maneja autenticacion API.

Metodos:

- `registrar(Request $request)`: valida y crea usuarios.
- `login(Request $request)`: valida credenciales y crea token Sanctum.
- `perfil(Request $request)`: devuelve el usuario autenticado.
- `logout(Request $request)`: elimina el token actual.

Notas:

- La API usa tokens Sanctum.
- La web usa login con `Auth::login()` directamente en `routes/web.php`.

### DashboardController

Archivo:

```text
app/Http/Controllers/DashboardController.php
```

Maneja la pantalla Home.

Metodo:

- `index(Request $request)`

Responsabilidades:

- Contar pacientes.
- Contar dentistas.
- Contar citas.
- Contar tratamientos.
- Contar productos con stock bajo.
- Obtener citas de hoy.
- Obtener proximas citas.
- Obtener productos bajos.
- Generar calendario mensual dinamico.
- Permitir navegar meses con `?mes=YYYY-MM`.

Variables enviadas a la vista:

- `totalPacientes`
- `totalDentistas`
- `totalCitas`
- `totalTratamientos`
- `stockBajo`
- `citasHoy`
- `proximasCitas`
- `citasMes`
- `mesActual`
- `mesAnterior`
- `mesSiguiente`
- `mesSeleccionado`
- `inicioCalendario`
- `finCalendario`
- `hoy`
- `fechaCalendario`
- `productosBajos`

### PacienteController

Archivo:

```text
app/Http/Controllers/PacienteController.php
```

API REST de pacientes.

Metodos:

- `index()`: lista pacientes paginados.
- `store(Request $request)`: crea paciente.
- `show(Paciente $paciente)`: muestra paciente.
- `update(Request $request, Paciente $paciente)`: actualiza paciente.
- `destroy(Paciente $paciente)`: elimina paciente.

La interfaz web de pacientes se maneja principalmente con closures en `routes/web.php`.

### DentistaController

Archivo:

```text
app/Http/Controllers/DentistaController.php
```

API REST de dentistas.

Metodos:

- `index()`
- `store(Request $request)`
- `show(Dentista $dentista)`
- `update(Request $request, Dentista $dentista)`
- `destroy(Dentista $dentista)`

### CitaController

Archivo:

```text
app/Http/Controllers/CitaController.php
```

Controlador de citas API y algunas acciones web.

Metodos:

- `index()`: lista citas con paciente y dentista.
- `store(Request $request)`: crea cita.
- `show(Cita $cita)`: muestra cita.
- `update(Request $request, Cita $cita)`: actualiza cita.
- `destroy(Cita $cita)`: elimina cita.
- `storeWeb(Request $request)`: reutiliza `store()` para guardar desde formulario Blade.
- `updateWeb(Request $request, Cita $cita)`: reutiliza `update()` para actualizar desde formulario Blade.

Reglas importantes de negocio:

- No permite agendar citas en fechas u horas pasadas.
- Verifica que la cita este dentro del horario laboral del dentista.
- Evita empalmes de citas para el mismo dentista, misma fecha y horario.
- Calcula `hora_fin` usando `hora_inicio + duracion_minutos`.
- Ignora citas canceladas al revisar empalmes.

### ExpedienteController

Archivo:

```text
app/Http/Controllers/ExpedienteController.php
```

API REST de expedientes.

Metodos:

- `index()`
- `store(Request $request)`
- `show(Expediente $expediente)`
- `update(Request $request, Expediente $expediente)`
- `destroy(Expediente $expediente)`
- `indexWeb(Request $request)`
- `createWeb()`
- `storeWeb(Request $request)`
- `editWeb(Expediente $expediente)`
- `updateWeb(Request $request, Expediente $expediente)`
- `destroyWeb(Expediente $expediente)`
- `destroyDocumentoWeb(ExpedienteDocumento $documento)`

Logica especial:

- Permite adjuntar documentos clinicos al crear o editar expedientes.
- Archivos permitidos: PDF, JPG, JPEG y PNG.
- Tamano maximo por archivo: 5 MB.
- Los archivos se guardan en `storage/app/public/expedientes/{id}`.
- Para abrir archivos desde navegador se requiere `php artisan storage:link`.
- Al eliminar un expediente se eliminan tambien sus archivos adjuntos.

### TratamientoController

Archivo:

```text
app/Http/Controllers/TratamientoController.php
```

API REST de tratamientos.

Metodos:

- `index()`
- `store(Request $request)`
- `show(Tratamiento $tratamiento)`
- `update(Request $request, Tratamiento $tratamiento)`
- `destroy(Tratamiento $tratamiento)`

### RecetaController

Archivo:

```text
app/Http/Controllers/RecetaController.php
```

API REST de recetas.

Metodos:

- `index()`
- `store(Request $request)`
- `show(Receta $receta)`
- `update(Request $request, Receta $receta)`
- `destroy(Receta $receta)`

### InventarioController

Archivo:

```text
app/Http/Controllers/InventarioController.php
```

API REST de inventario.

Metodos:

- `index()`: lista productos.
- `store(Request $request)`: crea producto.
- `show(Inventario $inventario)`: muestra producto.
- `update(Request $request, Inventario $inventario)`: actualiza producto.
- `destroy(Inventario $inventario)`: elimina producto.
- `alertas()`: devuelve productos con stock bajo y proximos a caducar.

## 9. Rutas web

Archivo:

```text
routes/web.php
```

### Autenticacion web

- `GET /login`: muestra formulario de login.
- `POST /login`: procesa login.
- `POST /logout`: cierra sesion.

El login web:

1. Valida email y password.
2. Busca usuario por email.
3. Verifica password con `Hash::check`.
4. Inicia sesion con `Auth::login`.
5. Redirige a Home.

### Home

- `GET /`: redirige a Home.
- `GET /home`: muestra Home.
- `ANY /dashboard`: redirige a `/home`.

La ruta con nombre sigue siendo `dashboard` por compatibilidad interna.

### Pacientes web

- `GET /vista-pacientes`
- `GET /vista-pacientes/crear`
- `POST /vista-pacientes/guardar`
- `GET /vista-pacientes/{paciente}/editar`
- `PUT /vista-pacientes/{paciente}/actualizar`
- `DELETE /vista-pacientes/{paciente}/eliminar`

Logica especial:

- La lista de pacientes tiene busqueda dinamica por nombre y apellidos.
- La busqueda usa `?buscar=texto`.
- Si la peticion es AJAX devuelve solo el parcial `pacientes.partials.tabla`.
- Si la peticion es normal devuelve la vista completa.

### Dentistas web

- `GET /vista-dentistas`
- `GET /vista-dentistas/crear`
- `POST /vista-dentistas/guardar`
- `GET /vista-dentistas/{dentista}/editar`
- `PUT /vista-dentistas/{dentista}/actualizar`
- `DELETE /vista-dentistas/{dentista}/eliminar`

### Citas web

- `GET /vista-citas`
- `GET /vista-citas/crear`
- `POST /vista-citas/guardar`
- `GET /vista-citas/{cita}/editar`
- `PUT /vista-citas/{cita}/actualizar`
- `PUT /vista-citas/{cita}/cancelar`
- `DELETE /vista-citas/{cita}/eliminar`

### Inventario web

- `GET /vista-inventario`
- `GET /vista-inventario/crear`
- `POST /vista-inventario/guardar`
- `GET /vista-inventario/{inventario}/editar`
- `PUT /vista-inventario/{inventario}/actualizar`
- `DELETE /vista-inventario/{inventario}/eliminar`

### Expedientes web

- `GET /vista-expedientes`
- `GET /vista-expedientes/crear`
- `POST /vista-expedientes/guardar`
- `GET /vista-expedientes/{expediente}/editar`
- `PUT /vista-expedientes/{expediente}/actualizar`
- `DELETE /vista-expedientes/{expediente}/eliminar`

### Tratamientos web

- `GET /vista-tratamientos`
- `GET /vista-tratamientos/crear`
- `POST /vista-tratamientos/guardar`
- `GET /vista-tratamientos/{tratamiento}/editar`
- `PUT /vista-tratamientos/{tratamiento}/actualizar`
- `DELETE /vista-tratamientos/{tratamiento}/eliminar`

### Recetas web

- `GET /vista-recetas`
- `GET /vista-recetas/crear`
- `POST /vista-recetas/guardar`
- `GET /vista-recetas/{receta}/editar`
- `PUT /vista-recetas/{receta}/actualizar`
- `DELETE /vista-recetas/{receta}/eliminar`

### Rutas de prueba

- `GET /probar-job`: despacha `RevisarInventarioJob`.
- `GET /probar-recordatorios`: despacha recordatorios de citas.

### Configuracion

- `GET /configuracion`: muestra datos de la cuenta y formulario de cambio de contrasena.
- `PUT /configuracion/password`: actualiza la contrasena del usuario autenticado.

Reglas:

- Requiere contrasena actual.
- La nueva contrasena debe tener al menos 8 caracteres.
- La nueva contrasena debe confirmarse con `password_confirmation`.
- La contrasena se guarda hasheada por el cast `password => hashed` del modelo `User`.

## 10. Rutas API

Archivo:

```text
routes/api.php
```

### Publicas

- `GET /api/estado`
- `POST /api/registrar`
- `POST /api/login`

### Protegidas con Sanctum

Estas rutas estan dentro de:

```php
Route::middleware('auth:sanctum')->group(...)
```

Rutas protegidas:

- `GET /api/perfil`
- `POST /api/logout`
- `apiResource /api/pacientes`
- `apiResource /api/dentistas`
- `apiResource /api/citas`
- `apiResource /api/expedientes`
- `apiResource /api/tratamientos`
- `apiResource /api/recetas`
- `apiResource /api/inventarios`
- `GET /api/inventarios-alertas`

## 11. Vistas Blade

### Layout general

Archivo:

```text
resources/views/layouts/app.blade.php
```

Define la estructura visual principal:

- Sidebar teal estilo DentalCare.
- Menu lateral con modulos.
- Topbar con usuario.
- Contenedor principal.
- Mensajes de exito.
- Mensajes de error.
- Carga Bootstrap 5 desde CDN.
- CSS simple propio inspirado en los prototipos.

### Login

Archivo:

```text
resources/views/auth/login.blade.php
```

Pantalla dividida:

- Panel izquierdo teal con marca DentalCare.
- Tarjeta informativa.
- Panel derecho con formulario de acceso.

Campos:

- email
- password

### Home

Archivo:

```text
resources/views/dashboard.blade.php
```

Aunque el archivo se llama `dashboard.blade.php`, visualmente la pantalla se llama Home.

Componentes:

- Resumen de pacientes, dentistas, citas del dia y stock bajo.
- Calendario de citas.
- Navegacion del calendario:
  - Anterior
  - Hoy
  - Siguiente
- Modal de detalle de cita.
- Lista de proximas citas.
- Lista de inventario por revisar.

Logica del calendario:

- Recibe fechas desde `DashboardController`.
- Usa `?mes=YYYY-MM` para cambiar de mes.
- Muestra citas dentro de cada dia.
- Al hacer click en una cita abre un modal Bootstrap.

### Pacientes

Archivos:

```text
resources/views/pacientes/index.blade.php
resources/views/pacientes/create.blade.php
resources/views/pacientes/edit.blade.php
resources/views/pacientes/partials/tabla.blade.php
```

`index.blade.php`:

- Encabezado de modulo.
- Busqueda dinamica por nombre y apellidos.
- Contenedor de tabla.
- JavaScript simple con `fetch`.
- Tarjeta resumen del primer paciente listado.

`partials/tabla.blade.php`:

- Tabla de pacientes.
- Botones Editar y Eliminar.
- Paginacion.
- Se usa tanto para carga normal como para AJAX.

`create.blade.php`:

- Formulario para crear paciente.
- Secciones:
  - Datos personales.
  - Contacto y antecedentes.

`edit.blade.php`:

- Formulario para actualizar paciente.
- Misma organizacion que crear.

### Modulos restantes

Cada modulo tiene sus vistas:

```text
resources/views/citas/
resources/views/dentistas/
resources/views/expedientes/
resources/views/inventario/
resources/views/recetas/
resources/views/tratamientos/
```

Generalmente cada carpeta contiene:

- `index.blade.php`: listado.
- `create.blade.php`: formulario de creacion.
- `edit.blade.php`: formulario de edicion.

### Paginacion

Archivo:

```text
resources/views/vendor/pagination/bootstrap-5.blade.php
```

Personaliza la paginacion Bootstrap:

- Anterior.
- Numeros de pagina.
- Siguiente.

Se agrego para evitar la paginacion Tailwind/SVG por defecto y mantener Bootstrap simple.

## 12. JavaScript usado

No se usa React, Vue ni frameworks frontend.

Se usa JavaScript simple en Blade.

### Busqueda dinamica de pacientes

Archivo:

```text
resources/views/pacientes/index.blade.php
```

Logica:

1. Escucha el evento `input` en el campo buscar.
2. Espera 300ms con debounce.
3. Construye la URL con `?buscar=texto`.
4. Hace `fetch` a la misma ruta.
5. Envia header `X-Requested-With: XMLHttpRequest`.
6. Reemplaza el HTML de `#tablaPacientes`.
7. Actualiza la URL con `history.replaceState`.
8. Intercepta clicks de paginacion para cargar tambien por AJAX.

### Modal de cita en calendario

Archivo:

```text
resources/views/dashboard.blade.php
```

Logica:

1. Cada cita del calendario es un boton.
2. El boton tiene atributos `data-cita-*`.
3. Al abrir el modal Bootstrap, JavaScript lee esos datos.
4. Llena los campos del modal.
5. Actualiza el link "Editar cita".

## 13. Jobs

### RevisarInventarioJob

Archivo:

```text
app/Jobs/RevisarInventarioJob.php
```

Responsabilidad:

- Revisar productos con stock bajo.
- Revisar productos que caducan en 30 dias.
- Registrar alertas en logs.

Logica:

- Busca productos donde `cantidad <= stock_minimo`.
- Busca productos con `fecha_caducidad <= hoy + 30 dias`.
- Escribe advertencias con `Log::warning`.

### RecordatorioCitasJob

Archivo:

```text
app/Jobs/RecordatorioCitasJob.php
```

Responsabilidad:

- Buscar citas programadas para manana.
- Ignorar citas canceladas.
- Registrar recordatorios en logs.

Logica:

- Usa `Carbon::tomorrow()`.
- Carga relaciones `paciente` y `dentista`.
- Escribe el recordatorio con `Log::info`.

### RecordatorioCita

Archivo:

```text
app/Jobs/RecordatorioCita.php
```

Actualmente es un job vacio generado por Laravel.

## 14. Factories y seeders

### Factories

Archivos:

```text
database/factories/UserFactory.php
database/factories/PacienteFactory.php
database/factories/DentistaFactory.php
database/factories/CitaFactory.php
```

Se usan para generar datos de prueba.

### Seeders

Archivos:

```text
database/seeders/DatabaseSeeder.php
database/seeders/PacienteSeeder.php
database/seeders/DentistaSeeder.php
database/seeders/CitaSeeder.php
```

`DatabaseSeeder` llama:

- `PacienteSeeder`
- `DentistaSeeder`
- `CitaSeeder`

## 15. Autenticacion

### Web

La autenticacion web esta en `routes/web.php`.

Flujo:

1. El usuario entra a `/login`.
2. Envia formulario a `POST /login`.
3. Se valida email y password.
4. Se busca el usuario.
5. Se verifica password.
6. Se inicia sesion.
7. Se redirige a Home.

Credenciales usadas en ambiente local anteriormente:

```text
admin@consultorio.com
password
```

Credenciales del backup:

```text
admin@dentaltec.com
admin123
```

### API

La API usa Sanctum.

Flujo:

1. `POST /api/login`
2. Si credenciales son correctas, devuelve token.
3. El token se usa en header:

```text
Authorization: Bearer TOKEN
```

## 16. Logica de citas

La logica fuerte del sistema esta en `CitaController`.

Al crear cita:

1. Valida:
   - paciente existente.
   - dentista existente.
   - fecha.
   - hora de inicio.
   - duracion.
   - motivo.
2. Calcula hora final.
3. Rechaza si la cita es en el pasado.
4. Carga dentista.
5. Revisa horario laboral del dentista.
6. Revisa empalmes con otras citas.
7. Crea cita en estado `pendiente`.

Al actualizar:

1. Permite cambiar paciente, dentista, fecha, hora, duracion, motivo y estado.
2. Recalcula hora final.
3. Revisa horario laboral.
4. Revisa empalmes ignorando la misma cita.
5. Actualiza registro.

## 17. Logica de pacientes

La vista web de pacientes usa closure en `routes/web.php`.

### Listado

Ruta:

```text
GET /vista-pacientes
```

Permite:

- Listar pacientes paginados.
- Buscar dinamicamente por:
  - nombre
  - apellido paterno
  - apellido materno
  - nombre completo

Busqueda:

```text
/vista-pacientes?buscar=Sha
```

La busqueda usa `LOWER(...) LIKE` para que no dependa de mayusculas/minusculas.

### Creacion

Ruta:

```text
POST /vista-pacientes/guardar
```

Valida:

- `nombre` requerido.
- `apellido_paterno` requerido.
- `telefono` requerido.
- `correo` requerido, email y unico.
- `fecha_nacimiento` requerida.
- Otros campos opcionales.

### Actualizacion

Ruta:

```text
PUT /vista-pacientes/{paciente}/actualizar
```

Valida igual que creacion, pero permite conservar el mismo correo del paciente.

### Eliminacion

Ruta:

```text
DELETE /vista-pacientes/{paciente}/eliminar
```

Elimina el registro del paciente.

## 18. Logica de inventario

El modulo inventario administra productos y alertas.

### Alertas

Un producto entra en alerta de stock bajo si:

```text
cantidad <= stock_minimo
```

Un producto entra en alerta de caducidad si:

```text
fecha_caducidad <= hoy + 30 dias
```

La API de alertas esta en:

```text
GET /api/inventarios-alertas
```

## 19. Estilo visual

El estilo esta basado en Bootstrap simple.

Se agrego CSS pequeno en:

```text
resources/views/layouts/app.blade.php
resources/views/auth/login.blade.php
```

Inspiracion visual:

- Sidebar teal.
- Cards blancas.
- Fondos claros.
- Tablas limpias.
- Botones teal/cyan.
- Layout tipo sistema administrativo.

No se usan librerias visuales complejas.

## 20. Docker y despliegue

### Docker local

Levantar PostgreSQL:

```bash
docker compose up -d
```

Contenedores:

```text
postgres_consultorio_1
postgres_consultorio_2
```

### Servidor Google Cloud

Segun `comandos.md`:

```bash
ssh -i ~/.ssh/id_ed25519 ernestogomez2211@34.72.247.59
```

Servidor:

```text
http://34.72.247.59:8000
```

### Túnel SSH reverso

Configuracion descrita:

```text
Servidor Laravel: Google Cloud Debian
Base de datos: PostgreSQL en Docker local
Contenedor principal: postgres_consultorio_1
Puerto local BD: 5434
Puerto tunel en servidor: 15434
Conexion: SSH reverse tunnel
```

Concepto:

```text
Laravel en servidor Google
    -> 127.0.0.1:15434
    -> tunel SSH reverso
    -> maquina local
    -> Docker PostgreSQL 127.0.0.1:5434
```

Comando tipico:

```bash
ssh -N -R 15434:127.0.0.1:5434 -i ~/.ssh/id_ed25519 ernestogomez2211@34.72.247.59
```

## 21. Comandos frecuentes

### Instalar dependencias PHP

```bash
composer install
```

### Instalar dependencias Node

```bash
npm install
```

### Compilar frontend

```bash
npm run build
```

### Levantar servidor Laravel local

```bash
php artisan serve
```

### Migrar base de datos

```bash
php artisan migrate
```

### Limpiar cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Optimizar/limpiar todo

```bash
php artisan optimize:clear
```

### Cachear vistas

```bash
php artisan view:cache
```

### Ejecutar pruebas

```bash
php artisan test
```

Nota: actualmente el test de ejemplo puede fallar porque espera `200` en `/`, pero `/` redirige a Home/login con `302`. No indica necesariamente un problema funcional.

## 22. Flujo de despliegue recomendado

En servidor:

```bash
cd ~/proyectos/consultorio-dental
git pull origin main
composer install
npm install
npm run build
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan migrate
```

Si solo cambian vistas:

```bash
php artisan view:clear
```

Si cambian rutas:

```bash
php artisan route:clear
```

Si cambia `.env`:

```bash
php artisan config:clear
```

## 23. Resumen de responsabilidades por modulo

| Modulo | Responsabilidad principal | Archivos clave |
| --- | --- | --- |
| Auth | Login web y API | `AuthController.php`, `routes/web.php`, `auth/login.blade.php` |
| Home | Resumen y calendario | `DashboardController.php`, `dashboard.blade.php` |
| Pacientes | CRUD y busqueda dinamica | `Paciente.php`, `PacienteController.php`, `pacientes/*`, `routes/web.php` |
| Dentistas | CRUD de dentistas | `Dentista.php`, `DentistaController.php`, `dentistas/*` |
| Citas | Agenda y validacion de empalmes | `Cita.php`, `CitaController.php`, `citas/*` |
| Expedientes | Historial clinico | `Expediente.php`, `ExpedienteController.php`, `expedientes/*` |
| Tratamientos | Planes/procedimientos dentales | `Tratamiento.php`, `TratamientoController.php`, `tratamientos/*` |
| Recetas | Medicamentos e indicaciones | `Receta.php`, `RecetaController.php`, `recetas/*` |
| Inventario | Insumos, stock y caducidad | `Inventario.php`, `InventarioController.php`, `inventario/*`, `RevisarInventarioJob.php` |
| Configuracion | Cambio de contrasena del usuario autenticado | `routes/web.php`, `configuracion/index.blade.php`, `User.php` |

## 24. Puntos importantes para exponer el proyecto

- Es un sistema Laravel MVC.
- Usa PostgreSQL en Docker.
- Tiene interfaz web con Blade y Bootstrap.
- Tiene API REST con Sanctum.
- Tiene relaciones Eloquent entre pacientes, dentistas, citas, expedientes, tratamientos y recetas.
- La agenda valida empalmes y horarios de dentistas.
- El Home tiene calendario mensual dinamico.
- Pacientes tiene busqueda dinamica simple por nombre.
- Inventario tiene alertas de stock bajo y caducidad.
- Se puede desplegar en Google Cloud y conectar a PostgreSQL local mediante tunel SSH reverso.
