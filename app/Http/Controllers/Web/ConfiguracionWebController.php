<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ConfiguracionWebController extends Controller
{
    public function index()
    {
        return view('configuracion.index');
    }

    public function updatePassword(Request $request)
    {
        $datos = $request->validate([
            'password_actual' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $usuario = Auth::user();

        if (!Hash::check($datos['password_actual'], $usuario->password)) {
            return back()->withErrors([
                'password_actual' => 'La contraseña actual no es correcta.',
            ]);
        }

        $usuario->update([
            'password' => $datos['password'],
        ]);

        return back()->with('success', 'Contraseña actualizada correctamente');
    }
}
