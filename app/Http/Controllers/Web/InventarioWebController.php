<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Inventario;
use Illuminate\Http\Request;

class InventarioWebController extends Controller
{
    public function index()
    {
        return view('inventario.index', [
            'productos' => Inventario::orderBy('id', 'desc')->paginate(20)
        ]);
    }

    public function create()
    {
        return view('inventario.create');
    }

    public function store(Request $request)
    {
        $datos = $this->validarInventario($request);

        Inventario::create($datos);

        return redirect()->route('inventario.vista')
            ->with('success', 'Producto registrado correctamente');
    }

    public function edit(Inventario $inventario)
    {
        return view('inventario.edit', [
            'producto' => $inventario
        ]);
    }

    public function update(Request $request, Inventario $inventario)
    {
        $datos = $this->validarInventario($request);

        $inventario->update($datos);

        return redirect()->route('inventario.vista')
            ->with('success', 'Producto actualizado correctamente');
    }

    public function destroy(Inventario $inventario)
    {
        $inventario->delete();

        return redirect()->route('inventario.vista')
            ->with('success', 'Producto eliminado correctamente');
    }

    private function validarInventario(Request $request): array
    {
        $datos = $request->validate([
            'nombre'          => 'required|string|max:100',
            'categoria'       => 'required|string|max:100',
            'cantidad'        => 'required|integer|min:0',
            'stock_c1'        => 'nullable|integer|min:0',
            'stock_c2'        => 'nullable|integer|min:0',
            'stock_c3'        => 'nullable|integer|min:0',
            'stock_c4'        => 'nullable|integer|min:0',
            'stock_minimo'    => 'required|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
            'proveedor'       => 'nullable|string|max:100',
            'precio_unitario' => 'required|numeric|min:0',
        ]);

        $sumaConsultorios = ($datos['stock_c1'] ?? 0)
                          + ($datos['stock_c2'] ?? 0)
                          + ($datos['stock_c3'] ?? 0)
                          + ($datos['stock_c4'] ?? 0);

        if ($sumaConsultorios > $datos['cantidad']) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'stock_c1' => 'La suma del stock por consultorio (' . $sumaConsultorios . ') no puede superar el stock general (' . $datos['cantidad'] . ').',
            ]);
        }

        // Normalizar nulls a 0
        foreach (['stock_c1', 'stock_c2', 'stock_c3', 'stock_c4'] as $col) {
            $datos[$col] = $datos[$col] ?? 0;
        }

        return $datos;
    }

    public function alertas()
    {
        // Stock general bajo (cantidad <= stock_minimo)
        $stockBajoGeneral = \App\Models\Inventario::whereColumn('cantidad', '<=', 'stock_minimo')
            ->orderBy('cantidad', 'asc')
            ->get();

        // Stock bajo por consultorio (cada consultorio individualmente)
        $stockBajoConsultorios = [];
        foreach ([1, 2, 3, 4] as $n) {
            $col = "stock_c{$n}";
            $stockBajoConsultorios[$n] = \App\Models\Inventario::whereColumn($col, '<=', 'stock_minimo')
                ->where($col, '>', 0)
                ->orderBy($col, 'asc')
                ->get();
        }

        $proximosCaducar = \App\Models\Inventario::whereNotNull('fecha_caducidad')
            ->whereDate('fecha_caducidad', '<=', now()->addDays(30))
            ->orderBy('fecha_caducidad', 'asc')
            ->get();

        return view('inventario.alertas', [
            'stockBajoGeneral'       => $stockBajoGeneral,
            'stockBajoConsultorios'  => $stockBajoConsultorios,
            'proximosCaducar'        => $proximosCaducar,
        ]);
    }
}
