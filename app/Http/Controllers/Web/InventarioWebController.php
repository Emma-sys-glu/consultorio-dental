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
        return $request->validate([
            'nombre' => 'required|string|max:100',
            'categoria' => 'required|string|max:100',
            'cantidad' => 'required|integer|min:0',
            'stock_minimo' => 'required|integer|min:0',
            'fecha_caducidad' => 'nullable|date',
            'proveedor' => 'nullable|string|max:100',
            'precio_unitario' => 'required|numeric|min:0'
        ]);
    }
}
