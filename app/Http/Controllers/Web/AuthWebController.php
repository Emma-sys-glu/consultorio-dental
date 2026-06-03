<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
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

        return match ($usuario->rol) {
            'dentista' => redirect()->route('dashboard.dentista'),
            'paciente' => redirect()->route('dashboard.paciente'),
            default    => redirect()->route('dashboard'),
        };
    }

    public function logout()
    {
        Auth::logout();

        return redirect()->route('login');
    }
}
