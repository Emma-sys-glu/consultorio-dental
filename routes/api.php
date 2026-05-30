<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\DentistaController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ExpedienteController;
use App\Http\Controllers\TratamientoController;
use App\Http\Controllers\RecetaController;
use App\Http\Controllers\InventarioController;

Route::get('/estado', function () {
    return response()->json([
        'sistema' => 'Sistema Inteligente de Gestión para Consultorio Dental',
        'estado' => 'API funcionando correctamente',
        'base_datos' => 'PostgreSQL Docker',
        'alta_disponibilidad' => true
    ]);
});

Route::post('/registrar', [AuthController::class, 'registrar']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/perfil', [AuthController::class, 'perfil']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('pacientes', PacienteController::class);
    Route::apiResource('dentistas', DentistaController::class);
    Route::apiResource('citas', CitaController::class);
    Route::apiResource('expedientes', ExpedienteController::class);
    Route::apiResource('tratamientos', TratamientoController::class);
    Route::apiResource('recetas', RecetaController::class);
    Route::apiResource('inventarios', InventarioController::class);
    Route::get(
        '/inventarios-alertas',
        [InventarioController::class, 'alertas']
    );
});
