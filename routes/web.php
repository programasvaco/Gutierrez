<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\AlmacenController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\TraspasoController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\CxPagarController;

// Rutas públicas
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

// Rutas protegidas (requieren autenticación)
Route::middleware(['auth'])->group(function () {
    
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    // Dashboard
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    
    // Perfil de usuario
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    
    // Catálogos
    Route::resource('productos', ProductoController::class);
    Route::resource('almacenes', AlmacenController::class);
    Route::resource('proveedores', ProveedorController::class);
    
    // Operaciones
    Route::resource('compras', CompraController::class);
    
    // Traspasos
    Route::resource('traspasos', TraspasoController::class);
    Route::get('traspasos-por-recibir', [TraspasoController::class, 'porRecibir'])->name('traspasos.por-recibir');
    Route::post('traspasos/{traspaso}/poner-en-transito', [TraspasoController::class, 'ponerEnTransito'])->name('traspasos.poner-en-transito');
    Route::post('traspasos/{traspaso}/recibir', [TraspasoController::class, 'recibir'])->name('traspasos.recibir');
    Route::post('traspasos/{traspaso}/cancelar', [TraspasoController::class, 'cancelar'])->name('traspasos.cancelar');
    
    // Consultas - Inventarios
    Route::get('inventarios', [InventarioController::class, 'index'])->name('inventarios.index');
    Route::get('inventarios/stock-bajo', [InventarioController::class, 'stockBajo'])->name('inventarios.stock-bajo');
    Route::get('inventarios/consolidado', [InventarioController::class, 'consolidado'])->name('inventarios.consolidado');
    
    // Consultas - Cuentas por Pagar
    Route::get('cxpagar', [CxPagarController::class, 'index'])->name('cxpagar.index');
    Route::get('cxpagar/vencidas', [CxPagarController::class, 'vencidas'])->name('cxpagar.vencidas');
    Route::get('cxpagar/por-proveedor', [CxPagarController::class, 'porProveedor'])->name('cxpagar.por-proveedor');
    
    // Reportes - Kardex
    Route::get('kardex', [KardexController::class, 'index'])->name('kardex.index');
    Route::get('kardex/reporte', [KardexController::class, 'reporte'])->name('kardex.reporte');
    
    // Gestión de Usuarios (solo administradores)
    Route::middleware(['admin'])->group(function () {
        Route::resource('users', UserController::class);
    });
});
