# Manual Técnico — Sistema DentalTec
### Consultorio Dental · Laravel 13 · PostgreSQL 16 · Bootstrap 5

---

## 1. Pila Tecnológica

| Tecnología | Versión | Rol en el sistema |
|---|---|---|
| **PHP** | 8.4 | Lenguaje de servidor. Procesa toda la lógica de negocio |
| **Laravel** | 13 | Framework MVC. Gestiona rutas, controladores, modelos, jobs, middleware y ORM |
| **PostgreSQL 16** | 16 | Base de datos relacional. Almacena todos los datos clínicos y operativos |
| **Bitnami Replication** | — | Alta disponibilidad: réplica en tiempo real entre dos contenedores Docker |
| **Bootstrap 5** | 5.3.3 | Framework CSS del frontend. Estilos, grillas y componentes visuales |
| **Blade** | Laravel | Motor de plantillas. Genera HTML dinámico en el servidor |
| **Laravel Sanctum** | 4.3 | Autenticación API REST via Personal Access Tokens |
| **Apache 2** | — | Servidor web en producción. Recibe peticiones HTTP/HTTPS |
| **Let's Encrypt** | — | Certificado SSL. Habilita HTTPS (requerido por PWA y Service Workers) |
| **silviolleite/laravelpwa** | 2.0 | Genera Web App Manifest y registra el Service Worker |
| **minishlink/web-push** | 10.1 | Envía notificaciones push con protocolo VAPID |
| **Docker Compose** | — | Orquesta los dos contenedores PostgreSQL (principal + réplica) |
| **PHPUnit** | 12 | Framework de pruebas. Ejecuta 69 tests automáticos |
| **Carbon** | Laravel | Manejo de fechas y horas (validación de horarios, cálculos de duración) |

---

## 2. Estructura de Carpetas y Archivos

```
consultorio-dental/
├── app/
│   ├── Console/Commands/
│   ├── Http/
│   │   ├── Controllers/          ← API REST (JSON)
│   │   ├── Controllers/Web/      ← Controladores de vistas Blade
│   │   └── Middleware/
│   ├── Jobs/
│   ├── Models/
│   ├── Observers/
│   ├── Providers/
│   └── Services/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/views/
├── routes/
└── tests/
    ├── Feature/
    └── Unit/
```

---

## 3. Descripción de Cada Archivo

---

### BOOTSTRAP Y CONFIGURACIÓN

---

#### `bootstrap/app.php`
Punto de arranque de Laravel. Registra las rutas (`web.php`, `api.php`, `console.php`), inyecta `FailoverDatabaseMiddleware` como middleware **global** (se ejecuta en cada request), crea el alias `'rol'` para `RolMiddleware`, y configura que las rutas bajo `/api/*` devuelvan errores en formato JSON en lugar de HTML.

---

#### `public/index.php`
Único punto de entrada HTTP de toda la aplicación. Recibe **todas** las peticiones del servidor web, carga el autoloader de Composer y delega a Laravel. Sin este archivo no existe la aplicación web.

---

#### `routes/web.php`
Define todas las rutas que devuelven **HTML** (interfaz visual). Están agrupadas bajo el middleware `auth` (requieren sesión activa). Cada ruta incluye el middleware `rol:` con los roles permitidos. Contiene: login/logout, dashboard por rol, CRUD de citas/pacientes/dentistas/expedientes/tratamientos/recetas/inventario y las rutas push de PWA.

---

#### `routes/api.php`
Define las rutas **JSON REST** bajo `/api/`. Protegidas con `auth:sanctum` (token de API). Expone un `apiResource` para citas, pacientes, dentistas, expedientes, tratamientos, recetas e inventario. Devuelve JSON en todos los casos.

---

#### `routes/console.php`
Programa las **tareas automáticas** (cron). Registra `RecordatorioCitasJob` para correr diariamente a las 08:00 y `RevisarInventarioJob` a las 08:05. El scheduler de Laravel ejecuta esto mediante `php artisan schedule:run` en el crontab del servidor.

---

#### `config/database.php`
Define las conexiones a base de datos. Incluye dos conexiones PostgreSQL personalizadas: `pgsql_principal` (puerto 5444, contenedor activo) y `pgsql_respaldo` (puerto 5443, réplica). El middleware de failover conmuta entre ellas en tiempo de ejecución.

---

#### `config/laravelpwa.php`
Configuración del manifiesto PWA: nombre de la app (`Sistema Dental`), nombre corto (`DentalTec`), colores de tema (`#067a7e`), íconos por tamaño (72px a 512px), pantallas splash y accesos directos a Citas y Pacientes.

---

#### `config/pwa.php`
Expone las claves VAPID mediante `config()` en lugar de `env()`. Necesario porque en producción la caché de configuración (`config:cache`) congela los valores y `env()` deja de funcionar en las vistas Blade. Contiene `vapid_public_key`, `vapid_private_key` y `vapid_subject`.

---

#### `phpunit.xml`
Configura PHPUnit para el entorno de pruebas: usa SQLite en memoria (`DB_DATABASE=:memory:`), desactiva caché y colas, establece `APP_ENV=testing` e incluye las claves VAPID de prueba. Garantiza que los tests no toquen la base de datos de producción.

---

### MODELOS (Eloquent ORM)

Los modelos representan las tablas de la base de datos como objetos PHP. Eloquent convierte las filas en instancias y permite consultar, crear, actualizar y eliminar registros usando métodos encadenados en lugar de SQL crudo.

---

#### `app/Models/User.php`
**Tabla:** `users`
**Campos clave:** `name`, `email`, `password`, `rol`, `paciente_id`, `dentista_id`

Extiende `Authenticatable` de Laravel (permite autenticación). Tiene relaciones `belongsTo` con `Paciente` y `Dentista` para vincular el usuario a su perfil clínico. Incluye cuatro métodos de verificación de rol: `esAdministrador()`, `esRecepcionista()`, `esDentista()` y `esPaciente()`. Los campos `password` y `remember_token` están ocultos (`#[Hidden]`) para que nunca aparezcan en JSON o arrays.

---

#### `app/Models/Paciente.php`
**Tabla:** `pacientes`
**Campos clave:** `nombre`, `apellido_paterno`, `apellido_materno`, `telefono`, `correo`, `fecha_nacimiento`, `curp`, `tipo_sangre`, `alergias`, `antecedentes_medicos`

Modelo clínico central. Tiene relación `hasMany` con `Cita` (un paciente tiene muchas citas) y `hasOne` con `Expediente` (un paciente tiene un expediente único).

---

#### `app/Models/Dentista.php`
**Tabla:** `dentistas`
**Campos clave:** `nombre`, `apellido_paterno`, `especialidad`, `cedula_profesional`, `telefono`, `correo`, `horario_inicio`, `horario_fin`, `consultorio`

Contiene el horario laboral del dentista (`horario_inicio`, `horario_fin`) que el sistema usa para validar que una cita no quede fuera del rango permitido. Tiene relación `hasMany` con `Cita`.

---

#### `app/Models/Cita.php`
**Tabla:** `citas`
**Campos clave:** `paciente_id`, `dentista_id`, `fecha`, `hora_inicio`, `hora_fin`, `duracion_minutos`, `motivo`, `estado`

Núcleo operativo del sistema. `estado` puede ser: `pendiente`, `confirmada`, `cancelada`, `finalizada`. Las citas canceladas se excluyen de la detección de conflictos de horario. Tiene relaciones `belongsTo` con `Paciente` y `Dentista`.

---

#### `app/Models/Expediente.php`
**Tabla:** `expedientes`
**Campos clave:** `paciente_id`, `diagnostico`, `observaciones`, `procedimientos_realizados`, `evolucion_tratamiento`

Historial clínico del paciente. Relación `belongsTo` con `Paciente` y `hasMany` con `ExpedienteDocumento`. Restricción única: un paciente solo puede tener un expediente.

---

#### `app/Models/ExpedienteDocumento.php`
**Tabla:** `expediente_documentos`
Archivos adjuntos al expediente (radiografías, estudios, etc.). Relación `belongsTo` con `Expediente`.

---

#### `app/Models/Tratamiento.php`
**Tabla:** `tratamientos`
**Campos clave:** `paciente_id`, `dentista_id`, `expediente_id`, `cita_id`, `nombre`, `descripcion`, `costo`, `estado`, `fecha_inicio`, `fecha_fin`

Procedimientos dentales realizados al paciente. `estado` puede ser: `pendiente`, `en_proceso`, `finalizado`, `cancelado`. Vincula paciente, dentista, expediente y cita en un solo registro.

---

#### `app/Models/Receta.php`
**Tabla:** `recetas`
Prescripciones médicas emitidas por el dentista. Asociada a un paciente y dentista.

---

#### `app/Models/Inventario.php`
**Tabla:** `inventarios`
**Campos clave:** `nombre`, `categoria`, `cantidad`, `stock_minimo`, `fecha_caducidad`, `proveedor`, `precio_unitario`

Materiales e insumos del consultorio. El sistema compara `cantidad <= stock_minimo` para detectar alertas. La columna `fecha_caducidad` permite detectar productos próximos a vencer.

---

#### `app/Models/Notificacion.php`
**Tabla:** `notificaciones`
**Campos clave:** `paciente_id`, `tipo`, `titulo`, `mensaje`, `leida`

Mensajes del sistema dirigidos a un paciente. `tipo` puede ser: `recordatorio_cita`, `resultado_tratamiento`, `pago_pendiente`, `promocion`, `aviso`, `cita`. Al crearse, el `NotificacionObserver` dispara automáticamente el push al navegador del paciente.

---

#### `app/Models/PushSubscription.php`
**Tabla:** `push_subscriptions`
**Campos clave:** `user_id`, `endpoint`, `public_key`, `auth_token`

Almacena la suscripción push del navegador del usuario. El `endpoint` es la URL única generada por FCM/Mozilla. Las claves `public_key` y `auth_token` son necesarias para cifrar el mensaje con el protocolo VAPID. Relación `belongsTo` con `User`.

---

### CONTROLADORES API REST (`app/Http/Controllers/`)

Estos controladores reciben peticiones con `Accept: application/json` y **siempre devuelven JSON**. Los usa la capa web como biblioteca de lógica de negocio.

---

#### `app/Http/Controllers/CitaController.php`
El controlador más complejo del sistema. Contiene toda la lógica de validación de citas.

- **`index()`** — Lista todas las citas paginadas (10 por página) con paciente y dentista cargados. Responde `200`.
- **`store(Request $request)`** — Crea una cita tras validar: (1) campos requeridos, (2) si el usuario es paciente usa su propio `paciente_id`, (3) fecha no pasada, (4) dentro del horario laboral del dentista, (5) sin empalme con otras citas activas. Responde `201` (éxito), `422` (regla de negocio), `409` (conflicto de horario).
- **`show(Cita $cita)`** — Devuelve una cita con sus relaciones. Responde `200`.
- **`update(Request $request, Cita $cita)`** — Actualiza una cita aplicando las mismas 5 validaciones de `store`, excluyendo la cita actual del chequeo de empalme. Responde `200`, `422` o `409`.
- **`destroy(Cita $cita)`** — Elimina la cita. Responde `200`.
- **`storeWeb(Request $request)`** — Adaptador: llama a `store()`, si hay error redirige de vuelta con `withErrors`, si hay éxito redirige a la lista de citas.
- **`updateWeb(Request $request, Cita $cita)`** — Adaptador: igual que `storeWeb` pero para actualización.

---

#### `app/Http/Controllers/PacienteController.php`
CRUD API REST para pacientes. Responde JSON. Métodos: `index` (lista paginada), `store` (crear con validación de correo único), `show` (detalle), `update` (actualizar permitiendo mismo correo), `destroy` (eliminar).

---

#### `app/Http/Controllers/DentistaController.php`
CRUD API REST para dentistas. Misma estructura que `PacienteController`.

---

#### `app/Http/Controllers/ExpedienteController.php`
CRUD API REST para expedientes clínicos. Incluye subida y descarga de documentos adjuntos.

---

#### `app/Http/Controllers/TratamientoController.php`
CRUD API REST para tratamientos. Permite filtrar por paciente o dentista.

---

#### `app/Http/Controllers/RecetaController.php`
CRUD API REST para recetas médicas.

---

#### `app/Http/Controllers/InventarioController.php`
CRUD API REST para inventario.

---

#### `app/Http/Controllers/DashboardController.php` *(raíz, API)*
Devuelve estadísticas generales del sistema en JSON (totales de pacientes, citas, dentistas, etc.).

---

#### `app/Http/Controllers/AuthController.php`
Maneja autenticación para la API: login con Sanctum (devuelve token), logout (revoca token).

---

#### `app/Http/Controllers/Controller.php`
Clase base vacía que todos los controladores extienden. Hereda de `Illuminate\Routing\Controller`.

---

### CONTROLADORES WEB (`app/Http/Controllers/Web/`)

Estos controladores manejan las vistas Blade. Devuelven HTML o redirecciones.

---

#### `app/Http/Controllers/Web/AuthWebController.php`
Gestiona el ciclo de sesión web.

- **`showLogin()`** — Muestra el formulario de login (`auth/login.blade.php`).
- **`login(Request $request)`** — Valida credenciales, autentica con `Auth::login()` y redirige al dashboard según el `rol` del usuario usando `match()`.
- **`logout()`** — Cierra sesión con `Auth::logout()` y redirige a `/login`.

---

#### `app/Http/Controllers/Web/DashboardController.php`
Controla los tres dashboards diferenciados por rol.

- **`redirectToHome()`** — Lee el rol del usuario y hace `redirect()` al dashboard correcto. Lo usan las rutas `/` y `/dashboard`.
- **`index(Request $request)`** — Dashboard de **admin/recepcionista**: carga totales globales y calendario de citas del mes (filtrable por `?mes=YYYY-MM`). Si el usuario es dentista o paciente, redirige a su dashboard correcto.
- **`indexDentista(Request $request)`** — Dashboard del **dentista**: citas de hoy, próximas citas, tratamientos en proceso y calendario mensual filtrado por `dentista_id`.
- **`indexPaciente()`** — Dashboard del **paciente**: próxima cita futura, historial de citas, tratamientos y notificaciones no leídas filtrados por `paciente_id`.

---

#### `app/Http/Controllers/Web/CitaWebController.php`
Vistas de citas. Delega la lógica de negocio a `CitaController`.

- **`index()`** — Lista citas paginadas. Si el rol es `paciente` filtra por `paciente_id`; si es `dentista` filtra por `dentista_id`; admin/recepcionista ve todas.
- **`create()`** — Formulario de nueva cita con listas de pacientes y dentistas.
- **`store(Request $request)`** — Llama a `CitaController::storeWeb()` que internamente usa la lógica API.
- **`edit(Cita $cita)`** — Formulario de edición. Valida que el paciente/dentista no edite citas ajenas (403).
- **`update(Request $request, Cita $cita)`** — Llama a `CitaController::updateWeb()`.
- **`cancelar(Cita $cita)`** — Cambia `estado` a `cancelada`. Valida pertenencia de la cita al usuario.
- **`destroy(Cita $cita)`** — Elimina la cita. Valida pertenencia.

---

#### `app/Http/Controllers/Web/PacienteWebController.php`
CRUD visual de pacientes con búsqueda en tiempo real.

- **`index(Request $request)`** — Lista paginada. Si viene `?buscar=texto`, filtra por nombre, apellidos o nombre completo usando `LOWER(CONCAT(...))`. Si la petición es AJAX devuelve solo el partial de la tabla.
- **`create()`** / **`store()`** / **`edit()`** / **`update()`** / **`destroy()`** — CRUD estándar con validación vía método privado `validarPaciente()` que centraliza las reglas de validación.

---

#### `app/Http/Controllers/Web/DentistaWebController.php`
CRUD visual de dentistas. Misma estructura que `PacienteWebController`.

---

#### `app/Http/Controllers/Web/ExpedienteWebController.php`
CRUD visual de expedientes. Permite ver el expediente de un paciente específico, agregar documentos adjuntos y ver el historial de tratamientos vinculados.

---

#### `app/Http/Controllers/Web/TratamientoWebController.php`
CRUD visual de tratamientos. Permite ver y actualizar el estado (`pendiente` → `en_proceso` → `finalizado`).

---

#### `app/Http/Controllers/Web/RecetaWebController.php`
CRUD visual de recetas. Solo dentistas y administradores pueden crearlas o modificarlas.

---

#### `app/Http/Controllers/Web/InventarioWebController.php`
CRUD visual del inventario más vista de alertas.

- **`index()`** — Lista todos los productos paginados.
- **`alertas()`** — Muestra dos listas: (1) productos con `cantidad <= stock_minimo`, ordenados del más crítico al menos; (2) productos con `fecha_caducidad <= hoy + 30 días`, ordenados por urgencia.
- Métodos estándar de CRUD con validación vía `validarInventario()`.

---

#### `app/Http/Controllers/Web/NotificacionWebController.php`
Vista de notificaciones del paciente autenticado.

- **`index()`** — Lista las notificaciones del paciente ordenadas por fecha descendente. Al abrir la vista, marca todas como `leida = true`.

---

#### `app/Http/Controllers/Web/PushSubscriptionController.php`
Gestiona las suscripciones push del navegador.

- **`guardar(Request $request)`** — Recibe `endpoint`, `keys.p256dh` y `keys.auth` del navegador. Hace `updateOrCreate` por endpoint para evitar duplicados. Asocia la suscripción al usuario autenticado (`auth()->id()`).
- **`eliminar(Request $request)`** — Elimina todas las suscripciones del usuario actual.

---

#### `app/Http/Controllers/Web/ConfiguracionWebController.php`
Configuración de la cuenta del usuario.

- **`index()`** — Muestra el formulario de configuración.
- **`updatePassword(Request $request)`** — Valida que la contraseña actual sea correcta (`Hash::check`), que la nueva tenga mínimo 8 caracteres y que la confirmación coincida. Actualiza con `$usuario->update(['password' => $nuevaPassword])` (el cast `hashed` en el modelo aplica `bcrypt` automáticamente).

---

#### `app/Http/Controllers/Web/JobWebController.php`
Permite disparar manualmente los jobs asíncronos desde el navegador (útil para pruebas y demostración).

- **`probarInventario()`** — Despacha `RevisarInventarioJob::dispatch()` a la cola.
- **`probarRecordatorios()`** — Despacha `RecordatorioCitasJob::dispatch()` a la cola.

---

### MIDDLEWARE (`app/Http/Middleware/`)

---

#### `app/Http/Middleware/FailoverDatabaseMiddleware.php`
**Se ejecuta en cada request HTTP** (registrado como middleware global).

1. Si `APP_ENV=testing`, salta toda la lógica (evita conectarse a PostgreSQL durante tests).
2. Mide el tiempo de `SELECT 1` en `pgsql_principal` con `microtime()`.
3. Si responde en menos de 1500ms: usa `pgsql_principal`.
4. Si responde lento o lanza excepción: llama a `activar('pgsql_respaldo')`.
5. `activar()` hace `DB::purge('pgsql_respaldo')` (fuerza conexión fresca) y `Config::set('database.default', conexion)`.
6. El resto del request usa automáticamente la conexión activa sin cambiar ningún código de negocio.

---

#### `app/Http/Middleware/RolMiddleware.php`
Controla acceso a rutas por rol. Recibe los roles permitidos como argumentos variables (`...$roles`).

1. Verifica que el usuario esté autenticado.
2. Comprueba `in_array(auth()->user()->rol, $roles)`.
3. Si el rol no está permitido: `abort(403, 'No tienes permiso...')`.
4. Si está permitido: `$next($request)` (continúa).

---

### JOBS ASÍNCRONOS (`app/Jobs/`)

---

#### `app/Jobs/RecordatorioCitasJob.php`
**Cron:** Diariamente a las **08:00** (`routes/console.php`).

1. Calcula `Carbon::tomorrow()`.
2. Consulta todas las citas para mañana que no estén canceladas, cargando paciente y dentista.
3. Por cada cita, construye el mensaje personalizado con hora y nombre del dentista.
4. **Verifica idempotencia**: busca si ya existe una notificación con ese mensaje creada hoy (evita duplicados si el job se ejecuta más de una vez).
5. Si no existe: crea la `Notificacion` → el `NotificacionObserver` se dispara automáticamente → llega el push al celular del paciente.
6. Registra en log cada recordatorio creado.

---

#### `app/Jobs/RevisarInventarioJob.php`
**Cron:** Diariamente a las **08:05**.

1. Busca productos donde `cantidad <= stock_minimo` → registra `Log::warning` con nombre, cantidad actual y mínimo.
2. Busca productos con `fecha_caducidad <= hoy + 30 días` → registra `Log::warning` con nombre y fecha de vencimiento.
3. Los administradores pueden ver estas alertas en la vista `/vista-inventario-alertas` o consultando los logs del servidor.

---

### OBSERVER Y SERVICIO DE PUSH

---

#### `app/Observers/NotificacionObserver.php`
Registrado en `AppServiceProvider`. Se ejecuta automáticamente **después de cada `Notificacion::create()`**.

1. Verifica que la notificación tenga `paciente_id`.
2. Busca el usuario con `User::where('paciente_id', ...)->first()`.
3. Si no hay usuario asociado, termina sin error.
4. Llama a `PushNotificationService::enviarAlUsuario(userId, titulo, mensaje, url)`.

---

#### `app/Services/PushNotificationService.php`
Servicio que encapsula el envío de notificaciones push usando el protocolo Web Push.

- **Constructor** — Instancia `WebPush` con las claves VAPID leídas de `config()`. Las claves se obtienen de `config/pwa.php` (no de `env()` para evitar problemas con caché de configuración).
- **`enviarAlUsuario(int $userId, string $titulo, string $mensaje, string $url)`**:
  1. Consulta `PushSubscription::where('user_id', $userId)->get()`.
  2. Si no hay suscripciones, retorna sin hacer nada.
  3. Construye el `payload` JSON: `{title, body, url}`.
  4. Por cada suscripción: `webPush->queueNotification(Subscription::create({endpoint, publicKey, authToken}), payload)`.
  5. `webPush->flush()` firma con VAPID y envía todos los mensajes encolados al servidor de push (FCM/Mozilla).
  6. Si una suscripción está expirada (`isSubscriptionExpired()`), la elimina de la BD.

---

### COMMAND

---

#### `app/Console/Commands/TestPushNotification.php`
Comando artisan: `php artisan push:test [email]`

Muestra cuántas suscripciones push hay en la BD. Si hay 0, imprime instrucciones para subscribirse. Si hay suscripciones, envía un push de prueba a todos o al email especificado, mostrando éxito o error por suscripción.

---

### PROVIDER

---

#### `app/Providers/AppServiceProvider.php`
Se ejecuta al arrancar la aplicación.

- Activa Bootstrap 5 como paginador: `Paginator::useBootstrapFive()`.
- Registra el observer: `Notificacion::observe(NotificacionObserver::class)`.
- En entorno de producción: `URL::forceScheme('https')` para que todos los URLs generados usen HTTPS (necesario para que las rutas de push y el Service Worker funcionen con certificado SSL).

---

### VISTAS BLADE (`resources/views/`)

---

#### `resources/views/layouts/app.blade.php`
**Layout maestro** que todas las demás vistas extienden con `@extends('layouts.app')`. Contiene:
- `<head>` con meta CSRF, Bootstrap CSS y la directiva `@laravelPWA` (inyecta el link al manifiesto y el script de registro del Service Worker).
- Sidebar móvil como offcanvas de Bootstrap (botón hamburguesa en móvil).
- Sidebar desktop fijo (`position: fixed`) con efecto hover: se oculta parcialmente (solo 8px visibles) y se despliega al pasar el ratón.
- Menú del sidebar diferenciado por rol (`@php $rol = Auth::user()->rol @endphp`).
- Banner toast en esquina inferior derecha para activar notificaciones push (solo para usuarios autenticados).
- Script JS de suscripción push: detecta si ya hay suscripción activa, si no la hay y el permiso no fue denegado muestra el banner. El botón "Activar" dispara `Notification.requestPermission()` y luego `pushManager.subscribe()`. En caso de `AbortError` (suscripción conflictiva) hace `unsubscribe()` y reintenta.

---

#### `resources/views/auth/login.blade.php`
Formulario de inicio de sesión. Campos: `email` y `password`. Muestra errores de validación con `@error`. No extiende `app.blade.php` (el usuario aún no está autenticado).

---

#### `resources/views/dashboard.blade.php`
Dashboard de **administrador y recepcionista**. Muestra 7 tarjetas de estadísticas (total pacientes, dentistas, citas hoy, tratamientos, recetas, inventario, stock bajo) y un calendario mensual completo con todas las citas. Tiene navegación de mes anterior/siguiente con `?mes=YYYY-MM`.

---

#### `resources/views/dashboard-dentista.blade.php`
Dashboard del **dentista**. Muestra sus citas del día actual con hora y estado, las próximas 10 citas futuras, los tratamientos en proceso y un calendario mensual filtrado por su `dentista_id`.

---

#### `resources/views/dashboard-paciente.blade.php`
Dashboard del **paciente**. Muestra la próxima cita destacada (tarjeta con fecha grande), alerta de notificaciones no leídas, historial de las últimas 5 citas, últimos 5 tratamientos y últimas 5 notificaciones.

---

#### `resources/views/citas/index.blade.php`
Tabla paginada de citas con columnas: fecha, hora, paciente, dentista, motivo, estado. Botones para editar y cancelar. Los pacientes solo ven sus propias citas (el controlador ya filtra en consulta).

#### `resources/views/citas/create.blade.php`
Formulario de nueva cita. Selects para paciente y dentista, campos de fecha, hora inicio, duración y motivo. Muestra errores de validación (conflicto de horario, fecha pasada, etc.).

#### `resources/views/citas/edit.blade.php`
Formulario de edición de cita. Igual que `create` pero con los datos precargados y método `PUT`.

---

#### `resources/views/pacientes/index.blade.php`
Lista paginada de pacientes con búsqueda en tiempo real (el campo de búsqueda hace petición AJAX al mismo endpoint para refrescar solo la tabla sin recargar la página).

#### `resources/views/pacientes/partials/tabla.blade.php`
Partial que contiene solo el HTML de la tabla de pacientes. Lo devuelve `PacienteWebController::index()` cuando detecta `$request->ajax()`, permitiendo la búsqueda en tiempo real.

#### `resources/views/pacientes/create.blade.php`
Formulario completo de nuevo paciente con todos los campos clínicos (CURP, tipo de sangre, alergias, antecedentes médicos).

#### `resources/views/pacientes/edit.blade.php`
Formulario de edición de paciente con datos precargados.

---

#### `resources/views/inventario/alertas.blade.php`
Vista de alertas de inventario. Dos secciones: (1) productos con stock bajo ordenados del más crítico, (2) productos próximos a caducar ordenados por urgencia. Accesible solo para admin y recepcionista.

---

#### `resources/views/notificaciones/index.blade.php`
Lista de notificaciones del paciente autenticado. Muestra `tipo`, `titulo`, `mensaje` y fecha. Al acceder se marcan todas como leídas.

---

#### `resources/views/configuracion/index.blade.php`
Formulario de cambio de contraseña del usuario autenticado. Campos: contraseña actual, nueva contraseña, confirmación.

---

#### `resources/views/welcome.blade.php`
Página de bienvenida pública (no requiere autenticación). Punto de entrada visual al sistema con botón para ir al login.

---

#### `resources/views/vendor/laravelpwa/`
Vistas generadas por el paquete `silviolleite/laravelpwa`. Contiene la página offline (se muestra cuando el usuario abre la app sin conexión y la ruta no está en caché del Service Worker).

---

### SERVICE WORKER

---

#### `public/serviceworker.js`
Archivo JS ejecutado por el navegador en segundo plano, independiente de la página.

- **`install`** — Al instalarse, abre la caché `pwa-vTIMESTAMP` y guarda los archivos estáticos (`/offline`, íconos).
- **`activate`** — Al activarse, elimina cachés antiguas de versiones anteriores (`pwa-v*` que no sean la actual).
- **`fetch`** — Intercepta peticiones de red **solo GET**. Sirve desde caché si existe, si no hace la petición real, y si falla (sin conexión) devuelve la página `/offline`.
- **`push`** — Recibe el payload de notificación push del servidor de FCM/Mozilla. Intenta parsear como JSON (`{title, body, url}`); si falla trata el contenido como texto plano. Muestra la notificación nativa del sistema operativo con `showNotification()`.
- **`notificationclick`** — Al hacer click en la notificación, cierra el popup y abre la URL especificada en `data.url` con `clients.openWindow()`.

---

### BASE DE DATOS

---

#### `database/migrations/`
15 migraciones en orden cronológico. Cada archivo describe una modificación al esquema de BD.

| Archivo | Qué crea/modifica |
|---|---|
| `000_create_users_table` | Tabla `users` base de Laravel |
| `000_create_cache_table` | Tabla `cache` para el sistema de caché |
| `000_create_jobs_table` | Tablas `jobs` y `job_batches` para la cola asíncrona |
| `create_pacientes_table` | Tabla `pacientes` con datos clínicos y personales |
| `create_dentistas_table` | Tabla `dentistas` con horarios y especialidad |
| `create_citas_table` | Tabla `citas` con FK a pacientes y dentistas |
| `create_personal_access_tokens_table` | Tabla de tokens de Sanctum para la API |
| `add_rol_to_users_table` | Agrega columna `rol` a `users` |
| `create_expedientes_table` | Tabla `expedientes` (historia clínica) |
| `create_tratamientos_table` | Tabla `tratamientos` |
| `create_recetas_table` | Tabla `recetas` |
| `create_inventarios_table` | Tabla `inventarios` |
| `create_expediente_documentos_table` | Tabla de documentos adjuntos al expediente |
| `add_roles_to_users_table` | Agrega `paciente_id` y `dentista_id` a `users` |
| `create_notificacions_table` | Tabla `notificaciones` |
| `add_paciente_id_to_notificaciones` | Agrega FK `paciente_id` a notificaciones |
| `create_push_subscriptions_table` | Tabla `push_subscriptions` para notificaciones push |

---

#### `database/seeders/DatabaseSeeder.php`
Orquesta el orden de ejecución de todos los seeders respetando dependencias de FK: `Paciente → Dentista → User → Cita → Expediente → Tratamiento → Receta → Inventario → Notificacion`.

#### `database/seeders/UserSeeder.php`
Crea: 1 admin (`admin@dentaltec.com`), 3 recepcionistas, 50 dentistas (vinculados a registros de `dentistas`) y 200 pacientes (vinculados a registros de `pacientes`). Usa emails tipo `dentista_{id}@dentaltec.com` para evitar conflictos de unicidad.

#### `database/seeders/PacienteSeeder.php`
Crea 200 pacientes con datos reales usando Faker en español: nombres, apellidos, CURP, tipo de sangre, alergias y antecedentes médicos.

#### `database/seeders/DentistaSeeder.php`
Crea 50 dentistas con especialidades odontológicas reales, cédulas profesionales únicas y horarios de trabajo.

#### `database/seeders/CitaSeeder.php`
Crea 1000 citas distribuidas entre pacientes y dentistas existentes, con fechas futuras y pasadas, y estados variados.

#### `database/seeders/ExpedienteSeeder.php`
Crea un expediente por cada paciente usando `Paciente::chunk(500)` con `Expediente::insert($rows)` en lote para eficiencia.

#### `database/seeders/TratamientoSeeder.php`
Crea 2000 tratamientos con distribución ponderada de estados: 50% finalizado, 20% pendiente, 15% en_proceso, 15% cancelado.

#### `database/seeders/RecetaSeeder.php`
Crea 1500 recetas con medicamentos dentales reales, insertadas en lotes de 500.

#### `database/seeders/InventarioSeeder.php`
Crea 33 productos de inventario dental reales en 6 categorías: Material dental, Instrumental, Anestésico, Medicamento, Desechable, Accesorio.

#### `database/seeders/NotificacionSeeder.php`
Crea 500 notificaciones con 5 tipos distintos y mensajes con plantillas parametrizadas.

---

#### `database/factories/`

| Archivo | Para qué sirve en tests |
|---|---|
| `UserFactory.php` | Crea usuarios con rol `administrador` por defecto, password `password` hasheada |
| `PacienteFactory.php` | Crea pacientes con datos Faker: nombre, correo único, CURP, tipo de sangre |
| `DentistaFactory.php` | Crea dentistas con horario y especialidad aleatorios |
| `CitaFactory.php` | Crea citas en fechas futuras aleatorias (no se usa directamente en tests actuales) |

---

### TESTS (`tests/`)

---

#### `tests/TestCase.php`
Clase base de todos los tests. Usa `RefreshDatabase` (limpia y migra la BD SQLite en memoria antes de cada test) y `withoutMiddleware(VerifyCsrfToken::class)` (elimina la verificación CSRF en tests automatizados donde no hay navegador).

---

#### `tests/Feature/AuthTest.php`
**10 tests** de autenticación.
- Login con credenciales correctas → usuario autenticado.
- Login con password incorrecto → errores de sesión, usuario no autenticado.
- Login con email inexistente → error.
- Login sin email → falla validación.
- Admin/dentista/paciente → cada uno redirige a su dashboard correcto tras login.
- Logout → cierra sesión y redirige a `/login`.
- Rutas protegidas → sin autenticación redirigen a `/login`.

---

#### `tests/Feature/CitaTest.php`
**12 tests** de la lógica de citas (usa el endpoint API `/api/citas`).
- Crear cita válida → `201` y registro en BD.
- Crear cita con datos → verifica que el JSON devuelto tiene `data.motivo`.
- Cita con empalme (11:00-12:00 luego 11:30-12:30) → `409`.
- Citas consecutivas (10:00-11:00 y 11:00-12:00) → ambas `201` (no hay empalme).
- Cita en slot de cita cancelada → `201` (canceladas no bloquean horario).
- Cita en fecha pasada → `422`.
- Cita antes del horario del dentista → `422`.
- Cita que termina después del horario → `422`.
- Cita con `paciente_id` inexistente → `422`.
- Cita con duración menor a 15 minutos → `422`.
- Admin cancela cita → `estado` cambia a `cancelada` en BD.
- Paciente intenta cancelar cita ajena → `403`, estado no cambia.

---

#### `tests/Feature/DashboardTest.php`
**12 tests** de dashboards diferenciados por rol.
- Admin y recepcionista ven `dashboard` (vista `dashboard`).
- Dentista ve `dashboard.dentista` (vista `dashboard-dentista`).
- Paciente ve `dashboard.paciente` (vista `dashboard-paciente`).
- Cada rol accede a `/` y es redirigido a su dashboard correcto.
- Dentista/paciente que accede a `/home` (admin) es redirigido a su dashboard.
- Sin autenticación → redirige a `/login` desde cualquier dashboard.

---

#### `tests/Feature/PacienteTest.php`
**14 tests** de gestión de pacientes.
- Admin y recepcionista pueden listar pacientes.
- Paciente no puede ver la lista de pacientes → `403`.
- Admin/recepcionista crean paciente con datos válidos → registro en BD.
- Validación de campos: falla sin nombre, sin correo, correo inválido, correo duplicado, sin teléfono, sin fecha de nacimiento.
- Actualizar paciente: funciona y permite mantener el mismo correo.
- Eliminar paciente → desaparece de BD.

---

#### `tests/Unit/UserModelTest.php`
**8 tests** del modelo `User`.
- `esAdministrador()` retorna `true`/`false` según el rol.
- Cada método es exclusivo de su rol (admin no es dentista, etc.).
- `UserFactory` genera campos requeridos (`name`, `email`, `password`, `rol`).
- `toArray()` no expone `password` ni `remember_token`.

---

#### `tests/Unit/CitaValidacionTest.php`
**13 tests** de lógica pura sin HTTP ni BD.
- Cálculo de hora fin: `10:00 + 60min = 11:00`, `09:30 + 30min = 10:00`, `14:00 + 90min = 15:30`.
- Detección de empalme: segunda cita dentro de la primera → `true`; primera cubre segunda → `true`.
- Sin empalme: citas consecutivas → `false`; cita anterior → `false`; cita posterior → `false`.
- Horario laboral: cita dentro → válida; antes del inicio → inválida; termina después del fin → inválida.
- Fechas: `Carbon::tomorrow()` no es pasada; `Carbon::yesterday()` sí es pasada.

---

## 4. Flujo de Datos

```
NAVEGADOR
    │  HTTP Request
    ▼
APACHE 2 (SSL · Let's Encrypt)
    │
    ▼
public/index.php
    │
    ▼
bootstrap/app.php → carga rutas y middleware
    │
    ▼
┌─────────────────────────────────────────────┐
│  PIPELINE DE MIDDLEWARE (cada request)       │
│  1. FailoverDatabaseMiddleware               │
│     SELECT 1 en pgsql_principal              │
│     OK <1500ms → usa principal               │
│     Error/lento → conmuta a pgsql_respaldo   │
│  2. StartSession                             │
│  3. Authenticate (requiere sesión)           │
│  4. RolMiddleware (verifica permiso)         │
│  5. VerifyCsrfToken (valida token form)      │
└─────────────────────────────────────────────┘
    │
    ▼
ROUTER (web.php / api.php)
    │
    ▼
CONTROLADOR WEB o API
    │  llama a modelos Eloquent
    ▼
MODELO → Query Builder → PDO
    │
    ▼
POSTGRESQL (principal o respaldo según middleware)
    │
    ▼
Resultado al Controlador
    │
    ├── API  → response()->json([...], status)
    └── WEB  → view('...', datos) / redirect()
    │
    ▼
BLADE renderiza HTML con Bootstrap 5
    │
    ▼
RESPUESTA al navegador
```

---

## 5. Comandos Útiles

```bash
# Levantar el sistema completo
sudo docker compose -f docker-compose-replica.yml up -d
php artisan serve  # solo desarrollo local

# Ejecutar todos los tests (69 tests)
php artisan test --testdox

# Generar datos de prueba
php artisan migrate:fresh --seed

# Ver tareas programadas
php artisan schedule:list

# Probar notificación push
php artisan push:test

# Limpiar y regenerar caché de configuración
php artisan config:clear && php artisan config:cache

# Ver rutas registradas
php artisan route:list
```

---

*Generado el 2026-06-03 · Sistema DentalTec v1.0 · Laravel 13 · PHP 8.4*
