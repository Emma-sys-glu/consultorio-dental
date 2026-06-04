<?php

use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\CitaWebController;
use App\Http\Controllers\Web\ConfiguracionWebController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\DentistaWebController;
use App\Http\Controllers\Web\ExpedienteWebController;
use App\Http\Controllers\Web\InventarioWebController;
use App\Http\Controllers\Web\JobWebController;
use App\Http\Controllers\Web\PacienteWebController;
use App\Http\Controllers\Web\RecetaWebController;
use App\Http\Controllers\Web\TratamientoWebController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\NotificacionWebController;
use App\Http\Controllers\Web\PushSubscriptionController;

Route::get('/login', [AuthWebController::class, 'showLogin'])
    ->name('login');

Route::post('/login', [AuthWebController::class, 'login'])
    ->name('login.procesar');

Route::middleware('auth')->group(function () {

    Route::get('/notificaciones', [NotificacionWebController::class, 'index'])
    ->middleware('rol:paciente')
    ->name('notificaciones.index');

    Route::get('/vista-inventario-alertas', [InventarioWebController::class, 'alertas'])
    ->middleware('rol:administrador,recepcionista')
    ->name('inventario.alertas');

    Route::get('/', [DashboardController::class, 'redirectToHome']);
    Route::get('/dashboard', [DashboardController::class, 'redirectToHome']);

    Route::get('/home', [DashboardController::class, 'index'])
        ->name('dashboard');

    Route::get('/home-dentista', [DashboardController::class, 'indexDentista'])
        ->name('dashboard.dentista');

    Route::get('/home-paciente', [DashboardController::class, 'indexPaciente'])
        ->name('dashboard.paciente');

    Route::post('/logout', [AuthWebController::class, 'logout'])
        ->name('logout');

    Route::get('/vista-pacientes', [PacienteWebController::class, 'index'])
        ->middleware('rol:administrador,recepcionista')
        ->name('pacientes.vista');

    Route::get('/vista-pacientes/crear', [PacienteWebController::class, 'create'])
        ->middleware('rol:administrador,recepcionista')
        ->name('pacientes.crear');

    Route::post('/vista-pacientes/guardar', [PacienteWebController::class, 'store'])
        ->middleware('rol:administrador,recepcionista')
        ->name('pacientes.guardar');

    Route::get('/vista-pacientes/{paciente}', [PacienteWebController::class, 'show'])
        ->middleware('rol:administrador,recepcionista')
        ->name('pacientes.detalle');

    Route::get('/vista-pacientes/{paciente}/editar', [PacienteWebController::class, 'edit'])
        ->middleware('rol:administrador,recepcionista')
        ->name('pacientes.editar');

    Route::put('/vista-pacientes/{paciente}/actualizar', [PacienteWebController::class, 'update'])
        ->middleware('rol:administrador,recepcionista')
        ->name('pacientes.actualizar');

    Route::delete('/vista-pacientes/{paciente}/eliminar', [PacienteWebController::class, 'destroy'])
        ->middleware('rol:administrador,recepcionista')
        ->name('pacientes.eliminar');

    Route::get('/vista-dentistas', [DentistaWebController::class, 'index'])
        ->middleware('rol:administrador,recepcionista')
        ->name('dentistas.vista');

    Route::get('/vista-dentistas/crear', [DentistaWebController::class, 'create'])
        ->middleware('rol:administrador')
        ->name('dentistas.crear');

    Route::post('/vista-dentistas/guardar', [DentistaWebController::class, 'store'])
        ->middleware('rol:administrador')
        ->name('dentistas.guardar');

    Route::get('/vista-dentistas/{dentista}/editar', [DentistaWebController::class, 'edit'])
        ->middleware('rol:administrador')
        ->name('dentistas.editar');

    Route::put('/vista-dentistas/{dentista}/actualizar', [DentistaWebController::class, 'update'])
        ->middleware('rol:administrador')
        ->name('dentistas.actualizar');

    Route::delete('/vista-dentistas/{dentista}/eliminar', [DentistaWebController::class, 'destroy'])
        ->middleware('rol:administrador')
        ->name('dentistas.eliminar');

    Route::get('/vista-citas', [CitaWebController::class, 'index'])
        ->middleware('rol:administrador,recepcionista,dentista,paciente')
        ->name('citas.vista');

    Route::get('/vista-citas/crear', [CitaWebController::class, 'create'])
        ->middleware('rol:administrador,recepcionista,dentista,paciente')
        ->name('citas.crear');

    Route::post('/vista-citas/guardar', [CitaWebController::class, 'store'])
        ->middleware('rol:administrador,recepcionista,dentista,paciente')
        ->name('citas.guardar');

    Route::get('/vista-citas/{cita}/editar', [CitaWebController::class, 'edit'])
        ->middleware('rol:administrador,recepcionista,dentista,paciente')
        ->name('citas.editar');

    Route::put('/vista-citas/{cita}/actualizar', [CitaWebController::class, 'update'])
        ->middleware('rol:administrador,recepcionista,dentista,paciente')
        ->name('citas.actualizar');

    Route::put('/vista-citas/{cita}/cancelar', [CitaWebController::class, 'cancelar'])
        ->middleware('rol:administrador,recepcionista,dentista,paciente')
        ->name('citas.cancelar');

    Route::delete('/vista-citas/{cita}/eliminar', [CitaWebController::class, 'destroy'])
        ->middleware('rol:administrador,recepcionista,dentista')
        ->name('citas.eliminar');

    Route::get('/vista-recetas', [RecetaWebController::class, 'index'])
        ->middleware('rol:administrador,dentista,paciente')
        ->name('recetas.vista');

    Route::get('/vista-recetas/crear', [RecetaWebController::class, 'create'])
        ->middleware('rol:administrador,dentista')
        ->name('recetas.crear');

    Route::post('/vista-recetas/guardar', [RecetaWebController::class, 'store'])
        ->middleware('rol:administrador,dentista')
        ->name('recetas.guardar');

    Route::get('/vista-recetas/{receta}/editar', [RecetaWebController::class, 'edit'])
        ->middleware('rol:administrador,dentista')
        ->name('recetas.editar');

    Route::put('/vista-recetas/{receta}/actualizar', [RecetaWebController::class, 'update'])
        ->middleware('rol:administrador,dentista')
        ->name('recetas.actualizar');

    Route::delete('/vista-recetas/{receta}/eliminar', [RecetaWebController::class, 'destroy'])
        ->middleware('rol:administrador,dentista')
        ->name('recetas.eliminar');

    Route::get('/vista-expedientes', [ExpedienteWebController::class, 'index'])
        ->middleware('rol:administrador,dentista,recepcionista')
        ->name('expedientes.vista');

    Route::get('/vista-expedientes/{expediente}/detalle', [ExpedienteWebController::class, 'show'])
        ->middleware('rol:administrador,dentista,recepcionista')
        ->name('expedientes.detalle');

    Route::get('/vista-expedientes/crear', [ExpedienteWebController::class, 'create'])
        ->middleware('rol:administrador,dentista')
        ->name('expedientes.crear');

    Route::post('/vista-expedientes/guardar', [ExpedienteWebController::class, 'store'])
        ->middleware('rol:administrador,dentista')
        ->name('expedientes.guardar');

    Route::get('/vista-expedientes/{expediente}/editar', [ExpedienteWebController::class, 'edit'])
        ->middleware('rol:administrador,dentista,recepcionista')
        ->name('expedientes.editar');

    Route::put('/vista-expedientes/{expediente}/actualizar', [ExpedienteWebController::class, 'update'])
        ->middleware('rol:administrador,dentista')
        ->name('expedientes.actualizar');

    Route::delete('/vista-expedientes/{expediente}/eliminar', [ExpedienteWebController::class, 'destroy'])
        ->middleware('rol:administrador')
        ->name('expedientes.eliminar');

    Route::delete('/vista-expedientes/documentos/{documento}/eliminar', [ExpedienteWebController::class, 'destroyDocumento'])
        ->middleware('rol:administrador,dentista')
        ->name('expedientes.documentos.eliminar');

    Route::get('/vista-tratamientos', [TratamientoWebController::class, 'index'])
        ->middleware('rol:administrador,dentista')
        ->name('tratamientos.vista');

    Route::get('/vista-tratamientos/crear', [TratamientoWebController::class, 'create'])
        ->middleware('rol:administrador,dentista')
        ->name('tratamientos.crear');

    Route::post('/vista-tratamientos/guardar', [TratamientoWebController::class, 'store'])
        ->middleware('rol:administrador,dentista')
        ->name('tratamientos.guardar');

    Route::get('/vista-tratamientos/{tratamiento}/editar', [TratamientoWebController::class, 'edit'])
        ->middleware('rol:administrador,dentista')
        ->name('tratamientos.editar');

    Route::put('/vista-tratamientos/{tratamiento}/actualizar', [TratamientoWebController::class, 'update'])
        ->middleware('rol:administrador,dentista')
        ->name('tratamientos.actualizar');

    Route::delete('/vista-tratamientos/{tratamiento}/eliminar', [TratamientoWebController::class, 'destroy'])
        ->middleware('rol:administrador')
        ->name('tratamientos.eliminar');

    Route::get('/vista-inventario', [InventarioWebController::class, 'index'])
        ->middleware('rol:administrador,recepcionista')
        ->name('inventario.vista');

    Route::get('/vista-inventario/crear', [InventarioWebController::class, 'create'])
        ->middleware('rol:administrador,recepcionista')
        ->name('inventario.crear');

    Route::post('/vista-inventario/guardar', [InventarioWebController::class, 'store'])
        ->middleware('rol:administrador,recepcionista')
        ->name('inventario.guardar');

    Route::get('/vista-inventario/{inventario}/editar', [InventarioWebController::class, 'edit'])
        ->middleware('rol:administrador,recepcionista')
        ->name('inventario.editar');

    Route::put('/vista-inventario/{inventario}/actualizar', [InventarioWebController::class, 'update'])
        ->middleware('rol:administrador,recepcionista')
        ->name('inventario.actualizar');

    Route::delete('/vista-inventario/{inventario}/eliminar', [InventarioWebController::class, 'destroy'])
        ->middleware('rol:administrador')
        ->name('inventario.eliminar');

    Route::get('/configuracion', [ConfiguracionWebController::class, 'index'])
        ->name('configuracion.index');

    Route::put('/configuracion/password', [ConfiguracionWebController::class, 'updatePassword'])
        ->name('configuracion.password');

    Route::get('/probar-job', [JobWebController::class, 'probarInventario'])
        ->middleware('rol:administrador');

    Route::get('/probar-recordatorios', [JobWebController::class, 'probarRecordatorios'])
        ->middleware('rol:administrador');

    Route::get('/probar-recordatorio-2h', [JobWebController::class, 'probarRecordatorio2h'])
        ->middleware('rol:administrador');
});

// PWA — página sin conexión (pública)
Route::get('/offline', function () {
    return view('vendor.laravelpwa.offline');
})->name('pwa.offline');

// PWA Push — suscripciones (requieren auth)
Route::middleware('auth')->group(function () {
    Route::post('/push/subscribe',   [PushSubscriptionController::class, 'guardar'])->name('push.subscribe');
    Route::post('/push/unsubscribe', [PushSubscriptionController::class, 'eliminar'])->name('push.unsubscribe');
});