<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Models\Paciente;
use App\Models\Dentista;
use App\Models\Cita;
use App\Models\Inventario;
use App\Models\Expediente;
use App\Models\Tratamiento;
use App\Models\Receta;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Jobs\RevisarInventarioJob;
use App\Jobs\RecordatorioCitasJob;

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/home', [DashboardController::class, 'index'])
    ->name('dashboard');

Route::redirect('/dashboard', '/home');

Route::get('/vista-pacientes', function () {
    $buscar = request('buscar');

    $pacientes = Paciente::query()
        ->when($buscar, function ($query, $buscar) {
            $buscar = strtolower(trim($buscar));

            $query->where(function ($q) use ($buscar) {
                $q->whereRaw('LOWER(nombre) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(apellido_paterno) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw('LOWER(COALESCE(apellido_materno, \'\')) LIKE ?', ["%{$buscar}%"])
                    ->orWhereRaw("LOWER(CONCAT(nombre, ' ', apellido_paterno, ' ', COALESCE(apellido_materno, ''))) LIKE ?", ["%{$buscar}%"]);
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(20)
        ->withQueryString();

    if (request()->ajax()) {
        return view('pacientes.partials.tabla', [
            'pacientes' => $pacientes,
        ]);
    }

    return view('pacientes.index', [
        'pacientes' => $pacientes,
        'buscar' => $buscar,
    ]);
})->name('pacientes.vista');

Route::get('/vista-dentistas', function () {
    return view('dentistas.index', [
        'dentistas' => Dentista::orderBy('id', 'desc')->paginate(20)
    ]);
})->name('dentistas.vista');

Route::get('/vista-citas', function () {
    return view('citas.index', [
        'citas' => Cita::with(['paciente', 'dentista'])
            ->orderBy('fecha', 'desc')
            ->paginate(20)
    ]);
})->name('citas.vista');

Route::get('/vista-inventario', function () {
    return view('inventario.index', [
        'productos' => Inventario::orderBy('id', 'desc')->paginate(20)
    ]);
})->name('inventario.vista');

Route::get('/vista-expedientes', function () {
    return view('expedientes.index', [
        'expedientes' => Expediente::with('paciente')
            ->orderBy('id', 'desc')
            ->paginate(20)
    ]);
})->name('expedientes.vista');

Route::get('/vista-tratamientos', function () {
    return view('tratamientos.index', [
        'tratamientos' => Tratamiento::with(['paciente', 'dentista'])
            ->orderBy('id', 'desc')
            ->paginate(20)
    ]);
})->name('tratamientos.vista');

Route::get('/vista-recetas', function () {
    return view('recetas.index', [
        'recetas' => Receta::with(['paciente', 'dentista', 'tratamiento'])
            ->orderBy('id', 'desc')
            ->paginate(20)
    ]);
})->name('recetas.vista');

Route::get('/vista-pacientes/crear', function () {
    return view('pacientes.create');
})->name('pacientes.crear');

Route::post('/vista-pacientes/guardar', function (Request $request) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'apellido_paterno' => 'required|string|max:100',
        'apellido_materno' => 'nullable|string|max:100',
        'telefono' => 'required|string|max:20',
        'correo' => 'required|email|unique:pacientes,correo',
        'fecha_nacimiento' => 'required|date',
        'curp' => 'nullable|string|max:18',
        'tipo_sangre' => 'nullable|string|max:5',
        'alergias' => 'nullable|string|max:255',
        'antecedentes_medicos' => 'nullable|string'
    ]);

    \App\Models\Paciente::create($datos);

    return redirect()->route('pacientes.vista')
        ->with('success', 'Paciente registrado correctamente');
})->name('pacientes.guardar');

Route::get('/vista-dentistas/crear', function () {
    return view('dentistas.create');
})->name('dentistas.crear');

Route::post('/vista-dentistas/guardar', function (Request $request) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'apellido_paterno' => 'required|string|max:100',
        'apellido_materno' => 'nullable|string|max:100',
        'especialidad' => 'required|string|max:100',
        'cedula_profesional' => 'required|string|max:30|unique:dentistas,cedula_profesional',
        'telefono' => 'required|string|max:20',
        'correo' => 'required|email|unique:dentistas,correo',
        'horario_inicio' => 'required',
        'horario_fin' => 'required',
        'consultorio' => 'required|string|max:50'
    ]);

    \App\Models\Dentista::create($datos);

    return redirect()->route('dentistas.vista')
        ->with('success', 'Dentista registrado correctamente');
})->name('dentistas.guardar');

Route::get('/vista-citas/crear', function () {
    return view('citas.create', [
        'pacientes' => \App\Models\Paciente::orderBy('nombre')->get(),
        'dentistas' => \App\Models\Dentista::orderBy('nombre')->get(),
    ]);
})->name('citas.crear');

Route::post('/vista-citas/guardar', [\App\Http\Controllers\CitaController::class, 'storeWeb'])
->name('citas.guardar');

Route::post('/logout', function () {
    Auth::logout();

    return redirect()->route('login');
})->name('logout');    

Route::get('/vista-pacientes/{paciente}/editar', function (Paciente $paciente) {
    return view('pacientes.edit', [
        'paciente' => $paciente
    ]);
})->name('pacientes.editar');

Route::put('/vista-pacientes/{paciente}/actualizar', function (Request $request, Paciente $paciente) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'apellido_paterno' => 'required|string|max:100',
        'apellido_materno' => 'nullable|string|max:100',
        'telefono' => 'required|string|max:20',
        'correo' => 'required|email|unique:pacientes,correo,' . $paciente->id,
        'fecha_nacimiento' => 'required|date',
        'curp' => 'nullable|string|max:18',
        'tipo_sangre' => 'nullable|string|max:5',
        'alergias' => 'nullable|string|max:255',
        'antecedentes_medicos' => 'nullable|string'
    ]);

    $paciente->update($datos);

    return redirect()->route('pacientes.vista')
        ->with('success', 'Paciente actualizado correctamente');
})->name('pacientes.actualizar');

Route::delete('/vista-pacientes/{paciente}/eliminar', function (Paciente $paciente) {
    $paciente->delete();

    return redirect()->route('pacientes.vista')
        ->with('success', 'Paciente eliminado correctamente');
})->name('pacientes.eliminar');

Route::get('/vista-dentistas/{dentista}/editar', function (Dentista $dentista) {
    return view('dentistas.edit', [
        'dentista' => $dentista
    ]);
})->name('dentistas.editar');

Route::put('/vista-dentistas/{dentista}/actualizar', function (Request $request, Dentista $dentista) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'apellido_paterno' => 'required|string|max:100',
        'apellido_materno' => 'nullable|string|max:100',
        'especialidad' => 'required|string|max:100',
        'cedula_profesional' => 'required|string|max:30|unique:dentistas,cedula_profesional,' . $dentista->id,
        'telefono' => 'required|string|max:20',
        'correo' => 'required|email|unique:dentistas,correo,' . $dentista->id,
        'horario_inicio' => 'required',
        'horario_fin' => 'required',
        'consultorio' => 'required|string|max:50'
    ]);

    $dentista->update($datos);

    return redirect()->route('dentistas.vista')
        ->with('success', 'Dentista actualizado correctamente');
})->name('dentistas.actualizar');

Route::delete('/vista-dentistas/{dentista}/eliminar', function (Dentista $dentista) {
    $dentista->delete();

    return redirect()->route('dentistas.vista')
        ->with('success', 'Dentista eliminado correctamente');
})->name('dentistas.eliminar');

Route::get('/vista-citas/{cita}/editar', function (Cita $cita) {
    return view('citas.edit', [
        'cita' => $cita,
        'pacientes' => Paciente::orderBy('nombre')->get(),
        'dentistas' => Dentista::orderBy('nombre')->get(),
    ]);
})->name('citas.editar');

Route::put('/vista-citas/{cita}/actualizar', [\App\Http\Controllers\CitaController::class, 'updateWeb'])
    ->name('citas.actualizar');

Route::put('/vista-citas/{cita}/cancelar', function (Cita $cita) {
    $cita->update([
        'estado' => 'cancelada'
    ]);

    return redirect()->route('citas.vista')
        ->with('success', 'Cita cancelada correctamente');
})->name('citas.cancelar');

Route::delete('/vista-citas/{cita}/eliminar', function (Cita $cita) {
    $cita->delete();

    return redirect()->route('citas.vista')
        ->with('success', 'Cita eliminada correctamente');
})->name('citas.eliminar');

Route::get('/vista-inventario/crear', function () {
    return view('inventario.create');
})->name('inventario.crear');

Route::post('/vista-inventario/guardar', function (Request $request) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'categoria' => 'required|string|max:100',
        'cantidad' => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
        'fecha_caducidad' => 'nullable|date',
        'proveedor' => 'nullable|string|max:100',
        'precio_unitario' => 'required|numeric|min:0'
    ]);

    Inventario::create($datos);

    return redirect()->route('inventario.vista')
        ->with('success', 'Producto registrado correctamente');
})->name('inventario.guardar');

Route::get('/vista-inventario/{inventario}/editar', function (Inventario $inventario) {
    return view('inventario.edit', [
        'producto' => $inventario
    ]);
})->name('inventario.editar');

Route::put('/vista-inventario/{inventario}/actualizar', function (Request $request, Inventario $inventario) {
    $datos = $request->validate([
        'nombre' => 'required|string|max:100',
        'categoria' => 'required|string|max:100',
        'cantidad' => 'required|integer|min:0',
        'stock_minimo' => 'required|integer|min:0',
        'fecha_caducidad' => 'nullable|date',
        'proveedor' => 'nullable|string|max:100',
        'precio_unitario' => 'required|numeric|min:0'
    ]);

    $inventario->update($datos);

    return redirect()->route('inventario.vista')
        ->with('success', 'Producto actualizado correctamente');
})->name('inventario.actualizar');

Route::delete('/vista-inventario/{inventario}/eliminar', function (Inventario $inventario) {
    $inventario->delete();

    return redirect()->route('inventario.vista')
        ->with('success', 'Producto eliminado correctamente');
})->name('inventario.eliminar');

Route::get('/vista-expedientes/crear', function () {
    return view('expedientes.create', [
        'pacientes' => Paciente::orderBy('nombre')->get(),
    ]);
})->name('expedientes.crear');

Route::post('/vista-expedientes/guardar', function (Request $request) {
    $datos = $request->validate([
        'paciente_id' => 'required|exists:pacientes,id|unique:expedientes,paciente_id',
        'diagnostico' => 'nullable|string',
        'observaciones' => 'nullable|string',
        'procedimientos_realizados' => 'nullable|string',
        'evolucion_tratamiento' => 'nullable|string'
    ]);

    Expediente::create($datos);

    return redirect()->route('expedientes.vista')
        ->with('success', 'Expediente creado correctamente');
})->name('expedientes.guardar');

Route::get('/vista-expedientes/{expediente}/editar', function (Expediente $expediente) {
    return view('expedientes.edit', [
        'expediente' => $expediente,
        'pacientes' => Paciente::orderBy('nombre')->get(),
    ]);
})->name('expedientes.editar');

Route::put('/vista-expedientes/{expediente}/actualizar', function (Request $request, Expediente $expediente) {
    $datos = $request->validate([
        'diagnostico' => 'nullable|string',
        'observaciones' => 'nullable|string',
        'procedimientos_realizados' => 'nullable|string',
        'evolucion_tratamiento' => 'nullable|string'
    ]);

    $expediente->update($datos);

    return redirect()->route('expedientes.vista')
        ->with('success', 'Expediente actualizado correctamente');
})->name('expedientes.actualizar');

Route::delete('/vista-expedientes/{expediente}/eliminar', function (Expediente $expediente) {
    $expediente->delete();

    return redirect()->route('expedientes.vista')
        ->with('success', 'Expediente eliminado correctamente');
})->name('expedientes.eliminar');

Route::get('/vista-tratamientos/crear', function () {
    return view('tratamientos.create', [
        'pacientes' => Paciente::orderBy('nombre')->get(),
        'dentistas' => Dentista::orderBy('nombre')->get(),
        'expedientes' => Expediente::with('paciente')->orderBy('id')->get(),
        'citas' => Cita::with(['paciente', 'dentista'])->orderBy('fecha', 'desc')->get(),
    ]);
})->name('tratamientos.crear');

Route::post('/vista-tratamientos/guardar', function (Request $request) {
    $datos = $request->validate([
        'paciente_id' => 'required|exists:pacientes,id',
        'dentista_id' => 'required|exists:dentistas,id',
        'expediente_id' => 'required|exists:expedientes,id',
        'cita_id' => 'nullable|exists:citas,id',
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
        'costo' => 'required|numeric|min:0',
        'estado' => 'required|in:pendiente,en_proceso,finalizado,cancelado',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
    ]);

    Tratamiento::create($datos);

    return redirect()->route('tratamientos.vista')
        ->with('success', 'Tratamiento registrado correctamente');
})->name('tratamientos.guardar');

Route::get('/vista-tratamientos/{tratamiento}/editar', function (Tratamiento $tratamiento) {
    return view('tratamientos.edit', [
        'tratamiento' => $tratamiento,
        'pacientes' => Paciente::orderBy('nombre')->get(),
        'dentistas' => Dentista::orderBy('nombre')->get(),
        'expedientes' => Expediente::with('paciente')->orderBy('id')->get(),
        'citas' => Cita::with(['paciente', 'dentista'])->orderBy('fecha', 'desc')->get(),
    ]);
})->name('tratamientos.editar');

Route::put('/vista-tratamientos/{tratamiento}/actualizar', function (Request $request, Tratamiento $tratamiento) {
    $datos = $request->validate([
        'paciente_id' => 'required|exists:pacientes,id',
        'dentista_id' => 'required|exists:dentistas,id',
        'expediente_id' => 'required|exists:expedientes,id',
        'cita_id' => 'nullable|exists:citas,id',
        'nombre' => 'required|string|max:100',
        'descripcion' => 'nullable|string',
        'costo' => 'required|numeric|min:0',
        'estado' => 'required|in:pendiente,en_proceso,finalizado,cancelado',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio'
    ]);

    $tratamiento->update($datos);

    return redirect()->route('tratamientos.vista')
        ->with('success', 'Tratamiento actualizado correctamente');
})->name('tratamientos.actualizar');

Route::delete('/vista-tratamientos/{tratamiento}/eliminar', function (Tratamiento $tratamiento) {
    $tratamiento->delete();

    return redirect()->route('tratamientos.vista')
        ->with('success', 'Tratamiento eliminado correctamente');
})->name('tratamientos.eliminar');

Route::get('/vista-recetas/crear', function () {
    return view('recetas.create', [
        'pacientes' => Paciente::orderBy('nombre')->get(),
        'dentistas' => Dentista::orderBy('nombre')->get(),
        'tratamientos' => Tratamiento::with('paciente')->orderBy('id', 'desc')->get(),
    ]);
})->name('recetas.crear');

Route::post('/vista-recetas/guardar', function (Request $request) {
    $datos = $request->validate([
        'paciente_id' => 'required|exists:pacientes,id',
        'dentista_id' => 'required|exists:dentistas,id',
        'tratamiento_id' => 'nullable|exists:tratamientos,id',
        'medicamento' => 'required|string|max:150',
        'dosis' => 'required|string|max:100',
        'frecuencia' => 'required|string|max:100',
        'duracion' => 'required|string|max:100',
        'indicaciones' => 'nullable|string',
        'fecha_emision' => 'required|date'
    ]);

    Receta::create($datos);

    return redirect()->route('recetas.vista')
        ->with('success', 'Receta registrada correctamente');
})->name('recetas.guardar');

Route::get('/vista-recetas/{receta}/editar', function (Receta $receta) {
    return view('recetas.edit', [
        'receta' => $receta,
        'pacientes' => Paciente::orderBy('nombre')->get(),
        'dentistas' => Dentista::orderBy('nombre')->get(),
        'tratamientos' => Tratamiento::with('paciente')->orderBy('id', 'desc')->get(),
    ]);
})->name('recetas.editar');

Route::put('/vista-recetas/{receta}/actualizar', function (Request $request, Receta $receta) {
    $datos = $request->validate([
        'paciente_id' => 'required|exists:pacientes,id',
        'dentista_id' => 'required|exists:dentistas,id',
        'tratamiento_id' => 'nullable|exists:tratamientos,id',
        'medicamento' => 'required|string|max:150',
        'dosis' => 'required|string|max:100',
        'frecuencia' => 'required|string|max:100',
        'duracion' => 'required|string|max:100',
        'indicaciones' => 'nullable|string',
        'fecha_emision' => 'required|date'
    ]);

    $receta->update($datos);

    return redirect()->route('recetas.vista')
        ->with('success', 'Receta actualizada correctamente');
})->name('recetas.actualizar');

Route::delete('/vista-recetas/{receta}/eliminar', function (Receta $receta) {
    $receta->delete();

    return redirect()->route('recetas.vista')
        ->with('success', 'Receta eliminada correctamente');
})->name('recetas.eliminar');

});


Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', function (Request $request) {
    $datos = $request->validate([
        'email' => 'required|email',
        'password' => 'required'
    ]);

    $usuario = User::where('email', $datos['email'])->first();

    if (!$usuario || !Hash::check($datos['password'], $usuario->password)) {
        return back()->withErrors([
            'email' => 'Credenciales incorrectas'
        ])->withInput();
    }

    Auth::login($usuario);

    return redirect()->route('dashboard');
})->name('login.procesar');

Route::get('/probar-job', function () {

    RevisarInventarioJob::dispatch();

    return 'Job enviado correctamente';
});

Route::get('/probar-recordatorios', function () {

    RecordatorioCitasJob::dispatch();

    return 'Job de recordatorios enviado';
});
