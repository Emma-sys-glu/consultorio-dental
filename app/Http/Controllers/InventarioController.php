<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    public function index()
    {
        return response()->json([
            'mensaje' => 'Inventario',
            'data' => Inventario::paginate(20)
        ]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => 'required|string|max:100',
            'categoria' => 'required|string|max:100',
            'cantidad' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
            'proveedor' => 'nullable|string|max:100',
            'precio_unitario' => 'required|numeric|min:0'
        ]);

        $inventario = Inventario::create($datos);

        return response()->json([
            'mensaje' => 'Producto registrado correctamente',
            'data' => $inventario
        ], 201);
    }

    public function show(Inventario $inventario)
    {
        return response()->json($inventario);
    }

    public function update(Request $request, Inventario $inventario)
    {
        $datos = $request->validate([
            'nombre'          => 'sometimes|string|max:100',
            'categoria'       => 'sometimes|string|max:100',
            'cantidad'        => 'sometimes|integer|min:0',
            'stock_minimo'    => 'sometimes|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
            'proveedor'       => 'nullable|string|max:100',
            'precio_unitario' => 'sometimes|numeric|min:0',
        ]);

        $inventario->update($datos);

        return response()->json([
            'mensaje' => 'Producto actualizado',
            'data' => $inventario
        ]);
    }

    public function destroy(Inventario $inventario)
    {
        $inventario->delete();

        return response()->json([
            'mensaje' => 'Producto eliminado'
        ]);
    }

    public function alertas()
    {
        $hoy = Carbon::today();

        $stockBajo = Inventario::whereColumn(
            'cantidad',
            '<=',
            'stock_minimo'
        )->get();

        $caducar = Inventario::whereNotNull('fecha_caducidad')
            ->whereDate(
                'fecha_caducidad',
                '<=',
                $hoy->copy()->addDays(30)
            )
            ->get();

        return response()->json([
            'stock_bajo' => $stockBajo,
            'proximos_a_caducar' => $caducar
        ]);
    }
}