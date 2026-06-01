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

Route::get('/login', [AuthWebController::class, 'showLogin'])
    ->name('login');
Route::post('/login', [AuthWebController::class, 'login'])
    ->name('login.procesar');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'redirectToHome']);
    Route::get('/home', [DashboardController::class, 'index'])
        ->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'redirectToHome']);

    Route::post('/logout', [AuthWebController::class, 'logout'])
        ->name('logout');

    Route::get('/vista-pacientes', [PacienteWebController::class, 'index'])
        ->name('pacientes.vista');
    Route::get('/vista-pacientes/crear', [PacienteWebController::class, 'create'])
        ->name('pacientes.crear');
    Route::post('/vista-pacientes/guardar', [PacienteWebController::class, 'store'])
        ->name('pacientes.guardar');
    Route::get('/vista-pacientes/{paciente}/editar', [PacienteWebController::class, 'edit'])
        ->name('pacientes.editar');
    Route::put('/vista-pacientes/{paciente}/actualizar', [PacienteWebController::class, 'update'])
        ->name('pacientes.actualizar');
    Route::delete('/vista-pacientes/{paciente}/eliminar', [PacienteWebController::class, 'destroy'])
        ->name('pacientes.eliminar');

    Route::get('/vista-dentistas', [DentistaWebController::class, 'index'])
        ->name('dentistas.vista');
    Route::get('/vista-dentistas/crear', [DentistaWebController::class, 'create'])
        ->name('dentistas.crear');
    Route::post('/vista-dentistas/guardar', [DentistaWebController::class, 'store'])
        ->name('dentistas.guardar');
    Route::get('/vista-dentistas/{dentista}/editar', [DentistaWebController::class, 'edit'])
        ->name('dentistas.editar');
    Route::put('/vista-dentistas/{dentista}/actualizar', [DentistaWebController::class, 'update'])
        ->name('dentistas.actualizar');
    Route::delete('/vista-dentistas/{dentista}/eliminar', [DentistaWebController::class, 'destroy'])
        ->name('dentistas.eliminar');

    Route::get('/vista-citas', [CitaWebController::class, 'index'])
        ->name('citas.vista');
    Route::get('/vista-citas/crear', [CitaWebController::class, 'create'])
        ->name('citas.crear');
    Route::post('/vista-citas/guardar', [CitaWebController::class, 'store'])
        ->name('citas.guardar');
    Route::get('/vista-citas/{cita}/editar', [CitaWebController::class, 'edit'])
        ->name('citas.editar');
    Route::put('/vista-citas/{cita}/actualizar', [CitaWebController::class, 'update'])
        ->name('citas.actualizar');
    Route::put('/vista-citas/{cita}/cancelar', [CitaWebController::class, 'cancel'])
        ->name('citas.cancelar');
    Route::delete('/vista-citas/{cita}/eliminar', [CitaWebController::class, 'destroy'])
        ->name('citas.eliminar');

    Route::get('/vista-expedientes', [ExpedienteWebController::class, 'index'])
        ->name('expedientes.vista');
    Route::get('/vista-expedientes/crear', [ExpedienteWebController::class, 'create'])
        ->name('expedientes.crear');
    Route::post('/vista-expedientes/guardar', [ExpedienteWebController::class, 'store'])
        ->name('expedientes.guardar');
    Route::get('/vista-expedientes/{expediente}/editar', [ExpedienteWebController::class, 'edit'])
        ->name('expedientes.editar');
    Route::put('/vista-expedientes/{expediente}/actualizar', [ExpedienteWebController::class, 'update'])
        ->name('expedientes.actualizar');
    Route::delete('/vista-expedientes/{expediente}/eliminar', [ExpedienteWebController::class, 'destroy'])
        ->name('expedientes.eliminar');
    Route::delete('/vista-expedientes/documentos/{documento}/eliminar', [ExpedienteWebController::class, 'destroyDocumento'])
        ->name('expedientes.documentos.eliminar');

    Route::get('/vista-tratamientos', [TratamientoWebController::class, 'index'])
        ->name('tratamientos.vista');
    Route::get('/vista-tratamientos/crear', [TratamientoWebController::class, 'create'])
        ->name('tratamientos.crear');
    Route::post('/vista-tratamientos/guardar', [TratamientoWebController::class, 'store'])
        ->name('tratamientos.guardar');
    Route::get('/vista-tratamientos/{tratamiento}/editar', [TratamientoWebController::class, 'edit'])
        ->name('tratamientos.editar');
    Route::put('/vista-tratamientos/{tratamiento}/actualizar', [TratamientoWebController::class, 'update'])
        ->name('tratamientos.actualizar');
    Route::delete('/vista-tratamientos/{tratamiento}/eliminar', [TratamientoWebController::class, 'destroy'])
        ->name('tratamientos.eliminar');

    Route::get('/vista-recetas', [RecetaWebController::class, 'index'])
        ->name('recetas.vista');
    Route::get('/vista-recetas/crear', [RecetaWebController::class, 'create'])
        ->name('recetas.crear');
    Route::post('/vista-recetas/guardar', [RecetaWebController::class, 'store'])
        ->name('recetas.guardar');
    Route::get('/vista-recetas/{receta}/editar', [RecetaWebController::class, 'edit'])
        ->name('recetas.editar');
    Route::put('/vista-recetas/{receta}/actualizar', [RecetaWebController::class, 'update'])
        ->name('recetas.actualizar');
    Route::delete('/vista-recetas/{receta}/eliminar', [RecetaWebController::class, 'destroy'])
        ->name('recetas.eliminar');

    Route::get('/vista-inventario', [InventarioWebController::class, 'index'])
        ->name('inventario.vista');
    Route::get('/vista-inventario/crear', [InventarioWebController::class, 'create'])
        ->name('inventario.crear');
    Route::post('/vista-inventario/guardar', [InventarioWebController::class, 'store'])
        ->name('inventario.guardar');
    Route::get('/vista-inventario/{inventario}/editar', [InventarioWebController::class, 'edit'])
        ->name('inventario.editar');
    Route::put('/vista-inventario/{inventario}/actualizar', [InventarioWebController::class, 'update'])
        ->name('inventario.actualizar');
    Route::delete('/vista-inventario/{inventario}/eliminar', [InventarioWebController::class, 'destroy'])
        ->name('inventario.eliminar');

    Route::get('/configuracion', [ConfiguracionWebController::class, 'index'])
        ->name('configuracion.index');
    Route::put('/configuracion/password', [ConfiguracionWebController::class, 'updatePassword'])
        ->name('configuracion.password');

    Route::get('/probar-job', [JobWebController::class, 'probarInventario']);
    Route::get('/probar-recordatorios', [JobWebController::class, 'probarRecordatorios']);
});
