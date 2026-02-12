<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Buscar usuario
        $user = User::where('email', $credentials['email'])->first();

        // Verificar si el usuario existe
        if (!$user) {
            return back()->withErrors([
                'email' => 'Las credenciales no coinciden con nuestros registros.',
            ])->withInput($request->only('email'));
        }

        // Verificar si el usuario está activo
        if ($user->status !== 'activo') {
            return back()->withErrors([
                'email' => 'Tu cuenta está inactiva. Contacta al administrador.',
            ])->withInput($request->only('email'));
        }

        // Intentar autenticar
        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'));
        }

        return back()->withErrors([
            'email' => 'Las credenciales no coinciden con nuestros registros.',
        ])->withInput($request->only('email'));
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    /**
     * Dashboard principal
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Estadísticas generales
        $stats = [
            'productos' => \App\Models\Producto::count(),
            'almacenes' => \App\Models\Almacen::count(),
            'proveedores' => \App\Models\Proveedor::count(),
            'compras' => \App\Models\Compra::count(),
            'traspasos' => \App\Models\Traspaso::count(),
            'stock_bajo' => \App\Models\Inventario::join('productos', 'inventario.producto_id', '=', 'productos.id')
                ->whereRaw('inventario.existencia < productos.stock_min')
                ->count(),
            'cuentas_vencidas' => \App\Models\CxPagar::where('saldo', '>', 0)
                ->where('fecha_vencimiento', '<', now())
                ->count(),
            'total_pendiente' => \App\Models\CxPagar::where('saldo', '>', 0)->sum('saldo'),
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}
