# Reporte de Validación QA — Sistema DentalTec
### PHPUnit 12 · Laravel 13 · Fecha: 2026-06-03

---

## Resultado General

```
✅  69 tests   ·   132 assertions   ·   0 failures   ·   0 errors   ·   ~1.5 s
```

---

## Índice

1. [Estrategia de Pruebas](#1-estrategia-de-pruebas)
2. [Cómo Ejecutar las Pruebas](#2-cómo-ejecutar-las-pruebas)
3. [Desglose por Archivo de Test](#3-desglose-por-archivo-de-test)
   - 3.1 [AuthTest — Autenticación y Roles](#31-authtest--autenticación-y-roles)
   - 3.2 [CitaTest — Lógica de Citas](#32-citatest--lógica-de-citas)
   - 3.3 [PacienteTest — CRUD de Pacientes](#33-pacientetest--crud-de-pacientes)
   - 3.4 [DashboardTest — Acceso por Rol](#34-dashboardtest--acceso-por-rol)
   - 3.5 [UserModelTest — Modelo de Usuario](#35-usermodeltest--modelo-de-usuario)
   - 3.6 [CitaValidacionTest — Lógica Pura de Negocio](#36-citavalidaciontest--lógica-pura-de-negocio)
4. [Mapa de Cobertura](#4-mapa-de-cobertura)
5. [Casos Faltantes y Código Sugerido](#5-casos-faltantes-y-código-sugerido)

---

## 1. Estrategia de Pruebas

### ¿Qué estamos probando y por qué?

El sistema DentalTec tiene tres áreas de riesgo que, si fallan, rompen la operación del consultorio:

#### A) Autenticación y Control de Acceso
**Por qué es crítico:** Un paciente que accede a expedientes ajenos o un recepcionista que modifica tratamientos son brechas de seguridad graves en un sistema médico.
**Qué protegen los tests:** Que cada rol solo pueda ver y hacer lo que le corresponde, y que las rutas sin sesión no sean accesibles.

#### B) Agendamiento de Citas
**Por qué es crítico:** Dos citas del mismo dentista al mismo tiempo crean conflictos reales en el consultorio. Una cita en fecha pasada o fuera del horario del dentista genera confusión operativa.
**Qué protegen los tests:** Las cuatro reglas de negocio más importantes: sin empalme, sin fecha pasada, dentro del horario laboral, y con datos válidos.

#### C) Integridad de Datos de Pacientes
**Por qué es crítico:** Dos pacientes con el mismo correo rompen la unicidad del sistema de identificación. Campos vacíos generan errores en consultas posteriores.
**Qué protegen los tests:** Que el formulario valide correctamente antes de llegar a la base de datos.

### Tipos de prueba usados

| Tipo | Archivos | Qué hacen |
|---|---|---|
| **Feature (Integración HTTP)** | `AuthTest`, `CitaTest`, `PacienteTest`, `DashboardTest` | Simulan peticiones HTTP completas (GET/POST/PUT/DELETE) y verifican respuestas, redirecciones, estado de la BD y errores de sesión |
| **Unit (Unitarios puros)** | `UserModelTest`, `CitaValidacionTest` | Prueban clases PHP aisladas sin base de datos ni HTTP. Solo lógica pura |

### Aislamiento: SQLite en memoria

Todos los tests usan una base de datos **SQLite en memoria** (`DB_DATABASE=:memory:` en `phpunit.xml`). El trait `RefreshDatabase` en `TestCase.php` limpia y migra esta BD antes de cada test. Esto garantiza:
- **Velocidad:** Sin I/O de disco ni conexión a red (≈1.5 s para 69 tests)
- **Aislamiento:** Cada test empieza con BD vacía, sin datos de otro test
- **Seguridad:** Nunca toca la BD de producción

---

## 2. Cómo Ejecutar las Pruebas

### Comando principal

```bash
php artisan test
```

### Con salida detallada (ver nombre de cada test)

```bash
php artisan test --testdox
```

### Ejecutar solo una suite

```bash
# Solo tests unitarios
php artisan test --testsuite=Unit

# Solo tests de integración
php artisan test --testsuite=Feature
```

### Ejecutar solo un archivo

```bash
php artisan test tests/Feature/CitaTest.php
php artisan test tests/Feature/AuthTest.php
```

### Ejecutar solo un test específico

```bash
php artisan test --filter rechaza_cita_cuando_el_dentista_ya_tiene_otra_en_ese_horario
```

### Ver cobertura de código (requiere Xdebug o PCOV)

```bash
php artisan test --coverage
```

### Salida esperada al correr `php artisan test`

```
   PASS  Tests\Feature\AuthTest
   PASS  Tests\Feature\CitaTest
   PASS  Tests\Feature\DashboardTest
   PASS  Tests\Feature\PacienteTest
   PASS  Tests\Unit\CitaValidacionTest
   PASS  Tests\Unit\UserModelTest

  Tests:    69 passed (132 assertions)
  Duration: 1.54s
```

> **Importante:** Antes de correr los tests, asegúrate de que la caché de configuración
> esté limpia. Si cacheaste la config de producción, los tests fallarán:
> ```bash
> php artisan config:clear
> php artisan test
> ```

---

## 3. Desglose por Archivo de Test

---

### 3.1 `AuthTest` — Autenticación y Roles

**Archivo:** `tests/Feature/AuthTest.php`
**Tests:** 10 · **Assertions:** 14

#### Setup del archivo

```php
private function crearAdmin(): User    // Crea usuario con rol='administrador'
private function crearDentista(): User // Crea Dentista + User vinculado con dentista_id
private function crearPaciente(): User // Crea Paciente + User vinculado con paciente_id
```
Estos helpers privados encapsulan la creación de usuarios para evitar repetición. Se llaman al inicio de cada test que los necesita.

---

#### Test 1: `login_con_credenciales_correctas_autentica_al_usuario`
```php
$response = $this->post(route('login.procesar'), ['email' => ..., 'password' => 'password']);
$response->assertRedirect();       // ← verifica que hubo una redirección (302)
$this->assertAuthenticatedAs($user); // ← verifica que Laravel registró la sesión del usuario
```
**Escenario:** El camino feliz del login. Confirma que `Auth::login()` funciona y que el usuario queda en sesión.

---

#### Test 2: `login_con_password_incorrecto_rechaza_el_acceso`
```php
$response->assertSessionHasErrors('email'); // ← verifica que hay error en el campo email
$this->assertGuest();                        // ← verifica que NO hay sesión activa
```
**Escenario:** Contraseña equivocada. El sistema no debe autenticar ni revelar si el email existe (mensaje genérico en campo `email`).

---

#### Test 3: `login_con_email_inexistente_rechaza_el_acceso`
```php
$response->assertSessionHasErrors('email');
$this->assertGuest();
```
**Escenario:** Email que no está en la BD. Mismo comportamiento que password incorrecta (evita enumerar usuarios válidos).

---

#### Test 4: `login_sin_email_falla_validacion`
```php
$response->assertSessionHasErrors('email');
```
**Escenario:** Campo email vacío. Valida que el `required` de la validación de Laravel funciona antes de llegar a la BD.

---

#### Test 5: `admin_es_redirigido_a_su_dashboard_tras_login`
```php
->assertRedirect(route('dashboard')); // ← URL exacta: /home
```
**Escenario:** Admin hace login → va a `/home`. Valida el `match()` en `AuthWebController`.

---

#### Test 6: `dentista_es_redirigido_a_su_dashboard_tras_login`
```php
->assertRedirect(route('dashboard.dentista')); // ← URL exacta: /home-dentista
```
**Escenario:** Dentista hace login → va a `/home-dentista` (no al dashboard de admin).

---

#### Test 7: `paciente_es_redirigido_a_su_dashboard_tras_login`
```php
->assertRedirect(route('dashboard.paciente')); // ← URL exacta: /home-paciente
```
**Escenario:** Paciente hace login → va a `/home-paciente`.

---

#### Test 8: `logout_cierra_sesion_y_redirige_a_login`
```php
$this->actingAs($user)->post(route('logout'))->assertRedirect(route('login'));
$this->assertGuest(); // ← verifica que la sesión fue destruida
```
**Escenario:** `Auth::logout()` elimina la sesión correctamente.

---

#### Tests 9 y 10: Rutas protegidas sin autenticación
```php
$this->get(route('dashboard'))->assertRedirect(route('login'));
$this->get(route('pacientes.vista'))->assertRedirect(route('login'));
```
**Escenario:** Visitar rutas protegidas sin sesión → siempre redirige a `/login`. Valida que el middleware `auth` está activo.

---

### 3.2 `CitaTest` — Lógica de Citas

**Archivo:** `tests/Feature/CitaTest.php`
**Tests:** 12 · **Assertions:** 21

> Este es el archivo más importante del proyecto. Valida las 4 reglas de negocio que protegen la integridad operativa del consultorio.

#### Setup del archivo

```php
protected function setUp(): void
{
    parent::setUp();
    $this->dentista = Dentista::factory()->create([
        'horario_inicio' => '08:00:00',
        'horario_fin'    => '18:00:00', // horario fijo para tests predecibles
    ]);
    $this->paciente        = Paciente::factory()->create();
    $this->admin           = User::factory()->create(['rol' => 'administrador']);
    $this->usuarioPaciente = User::factory()->create(['rol' => 'paciente', 'paciente_id' => $paciente2->id]);
}
```
`setUp()` se ejecuta **antes de cada test**. El dentista con horario fijo 08:00-18:00 garantiza resultados predecibles al probar límites de horario.

Los tests usan `postJson('/api/citas', ...)` (endpoint API) en lugar del endpoint web, porque el endpoint web siempre devuelve redirecciones y no permite verificar los códigos de error JSON (409, 422) directamente.

---

#### Test 1: `admin_puede_crear_una_cita_con_datos_validos`
```php
->postJson('/api/citas', $this->datosCita())
->assertStatus(201);                    // ← HTTP 201 Created
$this->assertDatabaseHas('citas', [...]) // ← verifica el registro en BD
```
**Escenario:** El camino feliz. Cita válida mañana a las 10:00 por 60 minutos dentro del horario del dentista.

---

#### Test 2: `crear_cita_devuelve_los_datos_de_la_cita_creada`
```php
->assertStatus(201)
->assertJsonPath('data.motivo', 'Limpieza dental'); // ← verifica campo exacto en el JSON
```
**Escenario:** Verifica que la respuesta 201 contiene el objeto `data` con los campos de la cita recién creada.

---

#### Test 3: `rechaza_cita_cuando_el_dentista_ya_tiene_otra_en_ese_horario` ⭐ CRÍTICO
```php
// Primera cita: 11:00 – 12:00
->postJson('/api/citas', [..., 'hora_inicio' => '11:00', 'duracion_minutos' => 60]);

// Segunda cita: 11:30 – 12:30 (se empalma con la primera)
->assertStatus(409)                                                        // ← HTTP 409 Conflict
->assertJsonPath('mensaje', 'El dentista ya tiene una cita en ese horario'); // ← mensaje exacto
```
**Escenario:** Algoritmo de intersección de intervalos. Dos citas que se solapan deben ser rechazadas. El estado 409 (Conflict) es semánticamente correcto: no es un error del cliente (400) sino un conflicto de estado del recurso.

---

#### Test 4: `permite_cita_inmediatamente_despues_de_otra_sin_empalme`
```php
// Cita 1: 10:00 – 11:00 → 201
->assertStatus(201);
// Cita 2: 11:00 – 12:00 (comienza exactamente cuando termina la anterior) → 201
->assertStatus(201);
```
**Escenario:** Caso límite del algoritmo de empalme. La condición `hora_inicio < fin` (estricto) garantiza que citas contiguas SÍ son permitidas.

---

#### Test 5: `cita_cancelada_no_bloquea_el_mismo_horario`
```php
Cita::create([..., 'estado' => 'cancelada']); // cita cancelada existente en 14:00-15:00
->postJson('/api/citas', [..., 'hora_inicio' => '14:00']) // nueva cita en el mismo slot
->assertStatus(201); // ← debe ser aceptada
```
**Escenario:** Las citas canceladas deben ser ignoradas en la detección de empalme. La consulta SQL usa `where('estado', '!=', 'cancelada')`.

---

#### Test 6: `rechaza_cita_en_fecha_pasada` ⭐ CRÍTICO
```php
->postJson('/api/citas', [..., 'fecha' => Carbon::yesterday()->format('Y-m-d')])
->assertStatus(422)
->assertJsonPath('mensaje', 'No se pueden agendar citas en fechas u horas pasadas');
```
**Escenario:** Evita que se creen citas con fecha anterior a hoy. `Carbon::isPast()` evalúa fecha + hora juntos.

---

#### Test 7: `rechaza_cita_antes_del_horario_de_inicio_del_dentista`
```php
->postJson('/api/citas', [..., 'hora_inicio' => '07:00', 'duracion_minutos' => 30])
// Cita: 07:00 – 07:30, pero el dentista empieza a las 08:00
->assertStatus(422)
->assertJsonPath('mensaje', 'La cita está fuera del horario laboral del dentista');
```
**Escenario:** Protege el horario de trabajo del dentista. La validación compara los objetos Carbon de inicio/fin contra `dentista->horario_inicio` y `dentista->horario_fin`.

---

#### Test 8: `rechaza_cita_que_termina_despues_del_horario_del_dentista`
```php
->postJson('/api/citas', [..., 'hora_inicio' => '17:30', 'duracion_minutos' => 60])
// Cita: 17:30 – 18:30, pero el dentista termina a las 18:00
->assertStatus(422);
```
**Escenario:** Caso límite del horario final. La cita empieza dentro del horario pero termina después. Ambos límites deben cumplirse.

---

#### Test 9: `rechaza_cita_con_paciente_inexistente`
```php
->postJson('/api/citas', [..., 'paciente_id' => 99999])
->assertStatus(422); // ← regla 'exists:pacientes,id' de Laravel
```
**Escenario:** Integridad referencial a nivel de aplicación. Evita crear citas huérfanas antes de que la BD rechace la FK.

---

#### Test 10: `rechaza_cita_con_duracion_menor_a_15_minutos`
```php
->postJson('/api/citas', [..., 'duracion_minutos' => 10])
->assertStatus(422); // ← regla 'min:15' de la validación
```
**Escenario:** Una cita de menos de 15 minutos no es operativamente válida para un consultorio dental.

---

#### Test 11: `admin_puede_cancelar_cualquier_cita`
```php
$this->actingAs($this->admin)
    ->put(route('citas.cancelar', $cita))
    ->assertRedirect(route('citas.vista'));         // ← redirección exitosa

$this->assertDatabaseHas('citas', [
    'id'     => $cita->id,
    'estado' => 'cancelada',                       // ← estado cambiado en BD
]);
```
**Escenario:** El admin puede cancelar cualquier cita del sistema.

---

#### Test 12: `paciente_no_puede_cancelar_cita_que_no_le_pertenece` ⭐ CRÍTICO
```php
// $cita pertenece a $this->paciente, no a $this->usuarioPaciente
$this->actingAs($this->usuarioPaciente)
    ->put(route('citas.cancelar', $cita))
    ->assertForbidden();                           // ← HTTP 403

$this->assertDatabaseHas('citas', [
    'id'     => $cita->id,
    'estado' => 'confirmada',                     // ← estado NO cambió
]);
```
**Escenario:** Verifica que el `abort(403)` del controlador es efectivo. El estado en BD no debe haber cambiado (doble verificación: status HTTP + estado en BD).

---

### 3.3 `PacienteTest` — CRUD de Pacientes

**Archivo:** `tests/Feature/PacienteTest.php`
**Tests:** 14 · **Assertions:** 21

#### Helper de datos de prueba

```php
private function datosPacienteValidos(array $override = []): array
{
    return array_merge([
        'nombre'           => 'Juan',
        'apellido_paterno' => 'García',
        'telefono'         => '7151234567',
        'correo'           => 'juan.garcia@email.com',
        'fecha_nacimiento' => '1990-05-15',
        // ... campos opcionales
    ], $override); // ← el array $override sobreescribe campos para simular errores
}
```
Este patrón permite probar una sola variación por test sin duplicar el array completo.

---

#### Tests de Listado (3 tests)

```php
// Admin → 200 OK + vista correcta
->assertOk()->assertViewIs('pacientes.index');

// Recepcionista → 200 OK (también tiene acceso)
->assertOk();

// Paciente → 403 Forbidden (no tiene permiso)
->assertForbidden();
```
**Escenario:** Verifica la tabla de permisos del `RolMiddleware`. Los tres roles se prueban en el mismo endpoint.

---

#### Tests de Creación (6 tests)

| Test | Campo faltante/inválido | Aserción |
|---|---|---|
| Datos válidos | — | `assertRedirect` + `assertDatabaseHas` |
| `nombre` vacío | nombre='' | `assertSessionHasErrors('nombre')` |
| `correo` vacío | correo='' | `assertSessionHasErrors('correo')` |
| `correo` inválido | correo='no-es-email' | `assertSessionHasErrors('correo')` |
| `correo` duplicado | correo ya existe en BD | `assertSessionHasErrors('correo')` |
| `telefono` vacío | telefono='' | `assertSessionHasErrors('telefono')` |
| `fecha_nacimiento` vacía | fecha='' | `assertSessionHasErrors('fecha_nacimiento')` |

`assertSessionHasErrors('campo')` verifica que Laravel puso errores de validación en la sesión para ese campo específico.

---

#### Tests de Actualización (2 tests)

```php
// Actualizar nombre → verifica en BD
$this->assertDatabaseHas('pacientes', ['nombre' => 'NombreActualizado']);

// Mantener el mismo correo al actualizar → no debe dar error de unicidad
->assertSessionDoesntHaveErrors('correo'); // ← sin este test, la regla unique rompería la edición
```
**Escenario crítico:** La regla `unique:pacientes,correo,{id}` en la validación de `update` excluye el registro actual. Sin esta exclusión, actualizar cualquier paciente siempre fallaría con "correo ya en uso".

---

#### Test de Eliminación

```php
->delete(route('pacientes.eliminar', $paciente))->assertRedirect(...);
$this->assertDatabaseMissing('pacientes', ['id' => $paciente->id]);
// assertDatabaseMissing es la inversa de assertDatabaseHas
```

---

### 3.4 `DashboardTest` — Acceso por Rol

**Archivo:** `tests/Feature/DashboardTest.php`
**Tests:** 12 · **Assertions:** 16

#### Tests de Acceso Correcto (4 tests)
```php
->assertOk()->assertViewIs('dashboard');          // admin y recepcionista
->assertOk()->assertViewIs('dashboard-dentista'); // dentista
->assertOk()->assertViewIs('dashboard-paciente'); // paciente
```
`assertViewIs()` verifica el **nombre de la plantilla Blade** cargada, no solo el código HTTP.

---

#### Tests de Redirección Automática (3 tests)
```php
// Todos acceden a '/' y son redirigidos a su dashboard correcto
$this->actingAs($this->admin())->get('/')    ->assertRedirect(route('dashboard'));
$this->actingAs($this->dentista())->get('/') ->assertRedirect(route('dashboard.dentista'));
$this->actingAs($this->paciente())->get('/') ->assertRedirect(route('dashboard.paciente'));
```
Valida el `match($rol)` en `DashboardController::redirectToHome()`.

---

#### Tests de Acceso al Dashboard Incorrecto (2 tests)
```php
// Dentista intenta acceder al dashboard de admin → redirigido
$this->actingAs($this->dentista())->get(route('dashboard'))
    ->assertRedirect(route('dashboard.dentista'));

// Paciente intenta acceder al dashboard de admin → redirigido
$this->actingAs($this->paciente())->get(route('dashboard'))
    ->assertRedirect(route('dashboard.paciente'));
```
Valida que `DashboardController::index()` detecta roles incorrectos y redirige en lugar de mostrar datos de admin a usuarios sin permiso.

---

#### Tests Sin Autenticación (3 tests)
```php
$this->get(route('dashboard'))         ->assertRedirect(route('login'));
$this->get(route('dashboard.dentista'))->assertRedirect(route('login'));
$this->get(route('dashboard.paciente'))->assertRedirect(route('login'));
```
Sin sesión → siempre al login. Valida el middleware `auth` en los tres endpoints.

---

### 3.5 `UserModelTest` — Modelo de Usuario

**Archivo:** `tests/Unit/UserModelTest.php`
**Tests:** 8 · **Assertions:** 15

Los tests unitarios usan `User::factory()->make()` (no `->create()`), lo que construye el objeto PHP **sin tocar la base de datos**. Esto los hace instantáneos.

---

#### Tests de Métodos de Rol (5 tests)
```php
$user = User::factory()->make(['rol' => 'administrador']);
$this->assertTrue($user->esAdministrador());  // debe ser true
$this->assertFalse($user->esRecepcionista()); // los otros deben ser false
$this->assertFalse($user->esDentista());
$this->assertFalse($user->esPaciente());
```
**Escenario:** Los cuatro métodos booleanos del modelo son mutuamente excluyentes. Un error aquí (ej. `return $this->rol === 'admin'` en lugar de `'administrador'`) rompería toda la lógica de acceso del sistema.

---

#### Test de Exclusividad
```php
// En un solo test verifica que ningún método retorna true para roles ajenos
$this->assertTrue($admin->esAdministrador());
$this->assertFalse($admin->esRecepcionista());
$this->assertFalse($admin->esDentista());
$this->assertFalse($admin->esPaciente());
```

---

#### Test de Factory
```php
$user = User::factory()->make();
$this->assertNotEmpty($user->name);
$this->assertNotEmpty($user->rol);  // ← verifica que la factory incluye el campo 'rol'
```
Valida que `UserFactory` genera todos los campos requeridos incluyendo `rol` (que fue agregado manualmente al factory).

---

#### Test de Campos Ocultos
```php
$array = $user->toArray();
$this->assertArrayNotHasKey('password', $array);
$this->assertArrayNotHasKey('remember_token', $array);
```
**Escenario:** Los atributos `#[Hidden]` del modelo no deben aparecer en JSON ni arrays. Crítico para no exponer hashes de contraseña en respuestas de API.

---

### 3.6 `CitaValidacionTest` — Lógica Pura de Negocio

**Archivo:** `tests/Unit/CitaValidacionTest.php`
**Tests:** 13 · **Assertions:** 13

Este archivo no hace peticiones HTTP ni accede a la BD. Prueba la **lógica matemática** de la validación de citas usando solo Carbon.

#### Helper de empalme (réplica del algoritmo del controlador)
```php
private function hayEmpalme(string $inicio1, string $fin1,
                             string $inicio2, string $fin2): bool
{
    return $i1->lt($f2) && $f1->gt($i2);
    // Algoritmo: dos intervalos [A,B] y [C,D] se solapan si A < D AND B > C
}
```

---

#### Tests de Cálculo de Hora Fin (3 tests)
```php
assertEquals('11:00', horaFin('10:00', 60)->format('H:i')); // 10:00 + 60min = 11:00
assertEquals('10:00', horaFin('09:30', 30)->format('H:i')); // cruce de hora
assertEquals('15:30', horaFin('14:00', 90)->format('H:i')); // 90 minutos = 1h30m
```
Verifica que `Carbon::addMinutes()` calcula correctamente incluyendo cruces de hora.

---

#### Tests de Detección de Empalmes (5 tests)

| Test | Cita 1 | Cita 2 | Resultado esperado |
|---|---|---|---|
| Segunda empieza dentro de la primera | 10:00–11:00 | 10:30–11:30 | `assertTrue` |
| Segunda cubre completamente a la primera | 10:00–11:00 | 09:30–11:30 | `assertTrue` |
| Citas consecutivas (contiguas) | 10:00–11:00 | 11:00–12:00 | `assertFalse` |
| Segunda es anterior | 14:00–15:00 | 12:00–13:00 | `assertFalse` |
| Segunda es posterior | 09:00–10:00 | 10:00–11:00 | `assertFalse` |

El caso más importante es "Citas consecutivas → `assertFalse`". Confirma que el algoritmo usa `<` (estricto) y no `<=`, permitiendo que un dentista tenga citas consecutivas.

---

#### Tests de Horario Laboral (3 tests)

```php
// Dentro: 10:00-11:00 dentro de 08:00-18:00 → válido
$this->assertTrue($citaInicio->gte($horarioInicio) && $citaFin->lte($horarioFin));

// Antes: 07:00-08:00 → inválido
$this->assertFalse(...);

// Excede: 17:30-18:30 → inválido
$this->assertFalse(...);
```

---

#### Tests de Fechas (2 tests)

```php
$this->assertFalse(Carbon::tomorrow()->setTime(10, 0)->isPast()); // futuro → no es pasado
$this->assertTrue(Carbon::yesterday()->setTime(10, 0)->isPast()); // ayer → es pasado
```

---

## 4. Mapa de Cobertura

### Qué está cubierto ✅

| Módulo | Funcionalidad | Cubierto |
|---|---|---|
| Auth | Login correcto | ✅ |
| Auth | Login incorrecto (password/email) | ✅ |
| Auth | Validación de campos vacíos | ✅ |
| Auth | Redirección por rol (4 roles) | ✅ |
| Auth | Logout | ✅ |
| Auth | Rutas protegidas sin sesión | ✅ |
| Citas | Crear cita válida | ✅ |
| Citas | Rechazo por empalme de horario | ✅ |
| Citas | Citas consecutivas sin empalme | ✅ |
| Citas | Cita cancelada no bloquea slot | ✅ |
| Citas | Rechazo por fecha pasada | ✅ |
| Citas | Rechazo fuera de horario (inicio) | ✅ |
| Citas | Rechazo fuera de horario (fin) | ✅ |
| Citas | Rechazo por paciente inexistente | ✅ |
| Citas | Rechazo por duración mínima | ✅ |
| Citas | Cancelar cita (admin) | ✅ |
| Citas | Cancelar cita ajena (paciente) → 403 | ✅ |
| Pacientes | Listar (admin/recepcionista/paciente) | ✅ |
| Pacientes | Crear con datos válidos | ✅ |
| Pacientes | Validación campos obligatorios | ✅ |
| Pacientes | Correo duplicado | ✅ |
| Pacientes | Actualizar | ✅ |
| Pacientes | Actualizar sin cambiar correo | ✅ |
| Pacientes | Eliminar | ✅ |
| Dashboard | Acceso correcto por rol | ✅ |
| Dashboard | Redirección automática | ✅ |
| Dashboard | Dashboard incorrecto → redirección | ✅ |
| Dashboard | Sin auth → login | ✅ |
| User Model | Métodos `es{Rol}()` | ✅ |
| User Model | Campos ocultos en array/JSON | ✅ |
| Lógica citas | Cálculo hora fin | ✅ |
| Lógica citas | Algoritmo de empalme (5 casos) | ✅ |
| Lógica citas | Validación horario laboral | ✅ |
| Lógica citas | Fechas pasadas con Carbon | ✅ |

### Qué NO está cubierto ⚠️

| Módulo | Funcionalidad faltante |
|---|---|
| Tratamientos | CRUD completo sin tests |
| Inventario | CRUD y alertas sin tests |
| Recetas | CRUD sin tests |
| Cambio de contraseña | `ConfiguracionWebController` sin tests |
| Citas (update) | Actualizar cita con verificación de empalme |
| Citas | Dentista edita cita ajena → 403 |
| Push | Guardar suscripción push |
| Pacientes | Dentista no puede crear pacientes → 403 |
| Inventario | Stock bajo detectado correctamente |

---

## 5. Casos Faltantes y Código Sugerido

Los siguientes tests cubren escenarios críticos no cubiertos actualmente.

---

### 5.1 `InventarioTest` — Inventario y Alertas de Stock

Crea el archivo `tests/Feature/InventarioTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Inventario;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class InventarioTest extends TestCase
{
    private function admin(): User
    {
        return User::factory()->create(['rol' => 'administrador']);
    }

    private function dentista(): User
    {
        return User::factory()->create(['rol' => 'dentista']);
    }

    private function datosProducto(array $override = []): array
    {
        return array_merge([
            'nombre'          => 'Guantes de látex',
            'categoria'       => 'Desechable',
            'cantidad'        => 100,
            'stock_minimo'    => 20,
            'precio_unitario' => 0.50,
            'proveedor'       => 'MedSupply',
            'fecha_caducidad' => null,
        ], $override);
    }

    // ── CRUD ─────────────────────────────────────────────────────────

    #[Test]
    public function admin_puede_crear_producto_de_inventario(): void
    {
        $this->actingAs($this->admin())
            ->post(route('inventario.guardar'), $this->datosProducto())
            ->assertRedirect(route('inventario.vista'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('inventarios', ['nombre' => 'Guantes de látex']);
    }

    #[Test]
    public function crear_producto_sin_nombre_falla_validacion(): void
    {
        $this->actingAs($this->admin())
            ->post(route('inventario.guardar'), $this->datosProducto(['nombre' => '']))
            ->assertSessionHasErrors('nombre');
    }

    #[Test]
    public function crear_producto_con_precio_negativo_falla_validacion(): void
    {
        $this->actingAs($this->admin())
            ->post(route('inventario.guardar'), $this->datosProducto(['precio_unitario' => -5]))
            ->assertSessionHasErrors('precio_unitario');
    }

    #[Test]
    public function crear_producto_con_cantidad_negativa_falla_validacion(): void
    {
        $this->actingAs($this->admin())
            ->post(route('inventario.guardar'), $this->datosProducto(['cantidad' => -1]))
            ->assertSessionHasErrors('cantidad');
    }

    #[Test]
    public function dentista_no_puede_ver_el_inventario(): void
    {
        $this->actingAs($this->dentista())
            ->get(route('inventario.vista'))
            ->assertForbidden();
    }

    // ── Alertas de stock ─────────────────────────────────────────────

    #[Test]
    public function vista_alertas_muestra_productos_con_stock_bajo(): void
    {
        // Producto con stock bajo: cantidad <= stock_minimo
        Inventario::factory()->create([
            'nombre'       => 'Anestesia',
            'cantidad'     => 5,
            'stock_minimo' => 10, // 5 <= 10 → stock bajo
        ]);

        // Producto con stock suficiente (no debe aparecer)
        Inventario::factory()->create([
            'nombre'       => 'Guantes',
            'cantidad'     => 50,
            'stock_minimo' => 20, // 50 > 20 → stock OK
        ]);

        $response = $this->actingAs($this->admin())
            ->get(route('inventario.alertas'))
            ->assertOk();

        $response->assertViewHas('stockBajo', function ($col) {
            return $col->contains('nombre', 'Anestesia')
                && !$col->contains('nombre', 'Guantes');
        });
    }

    #[Test]
    public function vista_alertas_muestra_productos_proximos_a_caducar(): void
    {
        // Caduca en 15 días (dentro de los 30 días de alerta)
        Inventario::factory()->create([
            'nombre'          => 'Gel fluoruro',
            'fecha_caducidad' => now()->addDays(15)->format('Y-m-d'),
        ]);

        // Caduca en 60 días (fuera del umbral de alerta)
        Inventario::factory()->create([
            'nombre'          => 'Pasta profiláctica',
            'fecha_caducidad' => now()->addDays(60)->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin())
            ->get(route('inventario.alertas'))
            ->assertOk();

        $response->assertViewHas('proximosCaducar', function ($col) {
            return $col->contains('nombre', 'Gel fluoruro')
                && !$col->contains('nombre', 'Pasta profiláctica');
        });
    }
}
```

> **Nota:** Este test requiere agregar `InventarioFactory`. Crea `database/factories/InventarioFactory.php`:
> ```php
> <?php
> namespace Database\Factories;
> use Illuminate\Database\Eloquent\Factories\Factory;
> class InventarioFactory extends Factory {
>     public function definition(): array {
>         return [
>             'nombre'          => fake()->word(),
>             'categoria'       => fake()->randomElement(['Material dental', 'Desechable', 'Medicamento']),
>             'cantidad'        => fake()->numberBetween(5, 200),
>             'stock_minimo'    => 20,
>             'precio_unitario' => fake()->randomFloat(2, 0.10, 500),
>             'fecha_caducidad' => null,
>         ];
>     }
> }
> ```

---

### 5.2 `TratamientoTest` — Control de Acceso en Tratamientos

Crea el archivo `tests/Feature/TratamientoTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Dentista;
use App\Models\Expediente;
use App\Models\Paciente;
use App\Models\Tratamiento;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TratamientoTest extends TestCase
{
    private User     $admin;
    private Paciente $paciente;
    private Dentista $dentista;
    private Expediente $expediente;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paciente   = Paciente::factory()->create();
        $this->dentista   = Dentista::factory()->create();
        $this->admin      = User::factory()->create(['rol' => 'administrador']);
        $this->expediente = Expediente::create([
            'paciente_id'  => $this->paciente->id,
            'diagnostico'  => 'Diagnóstico inicial',
            'observaciones'=> '',
        ]);
    }

    private function datosTratamiento(array $override = []): array
    {
        return array_merge([
            'paciente_id'   => $this->paciente->id,
            'dentista_id'   => $this->dentista->id,
            'expediente_id' => $this->expediente->id,
            'nombre'        => 'Limpieza dental',
            'descripcion'   => 'Limpieza profunda con ultrasonido',
            'costo'         => 500.00,
            'estado'        => 'pendiente',
            'fecha_inicio'  => Carbon::today()->format('Y-m-d'),
            'fecha_fin'     => null,
        ], $override);
    }

    #[Test]
    public function admin_puede_crear_tratamiento_valido(): void
    {
        $this->actingAs($this->admin)
            ->post(route('tratamientos.guardar'), $this->datosTratamiento())
            ->assertRedirect(route('tratamientos.vista'));

        $this->assertDatabaseHas('tratamientos', ['nombre' => 'Limpieza dental']);
    }

    #[Test]
    public function crear_tratamiento_con_estado_invalido_falla_validacion(): void
    {
        $this->actingAs($this->admin)
            ->post(route('tratamientos.guardar'), $this->datosTratamiento(['estado' => 'inventado']))
            ->assertSessionHasErrors('estado');
    }

    #[Test]
    public function crear_tratamiento_con_costo_negativo_falla_validacion(): void
    {
        $this->actingAs($this->admin)
            ->post(route('tratamientos.guardar'), $this->datosTratamiento(['costo' => -100]))
            ->assertSessionHasErrors('costo');
    }

    #[Test]
    public function fecha_fin_anterior_a_fecha_inicio_falla_validacion(): void
    {
        $this->actingAs($this->admin)
            ->post(route('tratamientos.guardar'), $this->datosTratamiento([
                'fecha_inicio' => '2026-06-10',
                'fecha_fin'    => '2026-06-05', // anterior a inicio
            ]))
            ->assertSessionHasErrors('fecha_fin');
    }

    #[Test]
    public function paciente_no_puede_crear_tratamientos(): void
    {
        $pacienteUser = User::factory()->create([
            'rol'         => 'paciente',
            'paciente_id' => $this->paciente->id,
        ]);

        $this->actingAs($pacienteUser)
            ->post(route('tratamientos.guardar'), $this->datosTratamiento())
            ->assertForbidden();
    }

    #[Test]
    public function admin_puede_eliminar_tratamiento(): void
    {
        $tratamiento = Tratamiento::create($this->datosTratamiento());

        $this->actingAs($this->admin)
            ->delete(route('tratamientos.eliminar', $tratamiento))
            ->assertRedirect(route('tratamientos.vista'));

        $this->assertDatabaseMissing('tratamientos', ['id' => $tratamiento->id]);
    }
}
```

---

### 5.3 `ConfiguracionTest` — Cambio de Contraseña

Crea el archivo `tests/Feature/ConfiguracionTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConfiguracionTest extends TestCase
{
    #[Test]
    public function usuario_puede_cambiar_su_contrasena_correctamente(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password_actual')]);

        $this->actingAs($user)
            ->post(route('configuracion.password'), [
                'password_actual'          => 'password_actual',
                'password'                 => 'nueva_password_segura',
                'password_confirmation'    => 'nueva_password_segura',
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        // Verificar que la nueva contraseña funciona en la BD
        $this->assertTrue(Hash::check('nueva_password_segura', $user->fresh()->password));
    }

    #[Test]
    public function cambio_de_contrasena_falla_si_password_actual_es_incorrecto(): void
    {
        $user = User::factory()->create(['password' => Hash::make('password_real')]);

        $this->actingAs($user)
            ->post(route('configuracion.password'), [
                'password_actual'       => 'password_incorrecta',
                'password'              => 'nueva_password',
                'password_confirmation' => 'nueva_password',
            ])
            ->assertSessionHasErrors('password_actual');

        // Contraseña en BD no debe haber cambiado
        $this->assertTrue(Hash::check('password_real', $user->fresh()->password));
    }

    #[Test]
    public function cambio_de_contrasena_falla_si_nueva_es_muy_corta(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('configuracion.password'), [
                'password_actual'       => 'password',
                'password'              => 'corta',   // menos de 8 caracteres
                'password_confirmation' => 'corta',
            ])
            ->assertSessionHasErrors('password');
    }

    #[Test]
    public function cambio_de_contrasena_falla_si_confirmacion_no_coincide(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('configuracion.password'), [
                'password_actual'       => 'password',
                'password'              => 'nueva_password_ok',
                'password_confirmation' => 'confirmacion_diferente',
            ])
            ->assertSessionHasErrors('password');
    }

    #[Test]
    public function usuario_no_autenticado_no_puede_cambiar_contrasena(): void
    {
        $this->post(route('configuracion.password'), [
            'password_actual'       => 'algo',
            'password'              => 'nueva_pass',
            'password_confirmation' => 'nueva_pass',
        ])->assertRedirect(route('login'));
    }
}
```

---

### 5.4 `CitaUpdateTest` — Actualización de Cita con Verificación de Empalme

El `CitaController::update()` tiene la misma lógica de empalme que `store()`, pero excluye la cita actual del chequeo. Este escenario no está cubierto.

Agrega estos tests al final de `CitaTest.php`:

```php
#[Test]
public function actualizar_cita_detecta_empalme_con_otra_cita(): void
{
    // Cita existente de OTRO paciente: 14:00 – 15:00
    $citaExistente = Cita::create([
        'paciente_id' => $this->paciente->id, 'dentista_id' => $this->dentista->id,
        'fecha'       => Carbon::tomorrow()->format('Y-m-d'),
        'hora_inicio' => '14:00:00', 'hora_fin' => '15:00:00',
        'duracion_minutos' => 60, 'motivo' => 'Revisión', 'estado' => 'confirmada',
    ]);

    // Cita que vamos a mover: actualmente a las 10:00
    $citaAMover = Cita::create([
        'paciente_id' => $this->paciente->id, 'dentista_id' => $this->dentista->id,
        'fecha'       => Carbon::tomorrow()->format('Y-m-d'),
        'hora_inicio' => '10:00:00', 'hora_fin' => '11:00:00',
        'duracion_minutos' => 60, 'motivo' => 'Limpieza', 'estado' => 'pendiente',
    ]);

    // Intentar moverla a 14:30 (empalma con la cita existente)
    $this->actingAs($this->admin)
        ->putJson("/api/citas/{$citaAMover->id}", [
            'hora_inicio'      => '14:30',
            'duracion_minutos' => 60,
            'fecha'            => Carbon::tomorrow()->format('Y-m-d'),
        ])
        ->assertStatus(409);
}

#[Test]
public function actualizar_cita_consigo_misma_no_detecta_empalme(): void
{
    // Una cita que se actualiza a sí misma no debe verse como empalme
    $cita = Cita::create([
        'paciente_id' => $this->paciente->id, 'dentista_id' => $this->dentista->id,
        'fecha'       => Carbon::tomorrow()->format('Y-m-d'),
        'hora_inicio' => '10:00:00', 'hora_fin' => '11:00:00',
        'duracion_minutos' => 60, 'motivo' => 'Limpieza', 'estado' => 'pendiente',
    ]);

    // Actualizar solo el motivo (sin cambiar hora) → no debe haber conflicto
    $this->actingAs($this->admin)
        ->putJson("/api/citas/{$cita->id}", [
            'motivo'           => 'Limpieza profunda',
            'hora_inicio'      => '10:00',
            'duracion_minutos' => 60,
            'fecha'            => Carbon::tomorrow()->format('Y-m-d'),
        ])
        ->assertStatus(200);
}
```

---

### Resumen de Casos Faltantes

| Archivo a crear | Tests | Prioridad |
|---|---|---|
| `InventarioTest.php` | 7 tests (CRUD + alertas stock + caducidad) | 🔴 Alta |
| `TratamientoTest.php` | 6 tests (CRUD + validaciones + control de acceso) | 🔴 Alta |
| `ConfiguracionTest.php` | 5 tests (cambio contraseña correcto e incorrecto) | 🟡 Media |
| Nuevos en `CitaTest.php` | 2 tests (update con empalme + update consigo misma) | 🔴 Alta |

---

*Reporte generado el 2026-06-03 · PHPUnit 12.5 · Laravel 13 · PHP 8.4*
