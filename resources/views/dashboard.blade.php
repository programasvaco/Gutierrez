@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-tachometer-alt"></i> Dashboard</h2>
        <p class="text-muted">Bienvenido, <strong>{{ $user->name }}</strong></p>
    </div>
</div>

<!-- Estadísticas Principales -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Productos</h6>
                        <h2 class="mb-0">{{ $stats['productos'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-box fa-3x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('productos.index') }}" class="text-white text-decoration-none">
                    <small><i class="fas fa-arrow-right"></i> Ver catálogo</small>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Almacenes</h6>
                        <h2 class="mb-0">{{ $stats['almacenes'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-warehouse fa-3x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('almacenes.index') }}" class="text-white text-decoration-none">
                    <small><i class="fas fa-arrow-right"></i> Ver almacenes</small>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Proveedores</h6>
                        <h2 class="mb-0">{{ $stats['proveedores'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-truck fa-3x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('proveedores.index') }}" class="text-white text-decoration-none">
                    <small><i class="fas fa-arrow-right"></i> Ver proveedores</small>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Compras</h6>
                        <h2 class="mb-0">{{ $stats['compras'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-shopping-cart fa-3x opacity-50"></i>
                    </div>
                </div>
                <a href="{{ route('compras.index') }}" class="text-white text-decoration-none">
                    <small><i class="fas fa-arrow-right"></i> Ver compras</small>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Alertas y Notificaciones -->
<div class="row mb-4">
    <div class="col-md-6 mb-3">
        <div class="card border-warning">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Stock Bajo
                </h5>
            </div>
            <div class="card-body">
                @if($stats['stock_bajo'] > 0)
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Hay <strong>{{ $stats['stock_bajo'] }}</strong> productos con stock bajo.
                    </div>
                    <a href="{{ route('inventarios.stock-bajo') }}" class="btn btn-warning btn-sm mt-2">
                        <i class="fas fa-eye"></i> Ver Productos
                    </a>
                @else
                    <p class="text-success mb-0">
                        <i class="fas fa-check-circle"></i> Todos los productos tienen stock adecuado.
                    </p>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card border-danger">
            <div class="card-header bg-danger text-white">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-circle"></i> Cuentas Vencidas
                </h5>
            </div>
            <div class="card-body">
                @if($stats['cuentas_vencidas'] > 0)
                    <div class="alert alert-danger mb-0">
                        <i class="fas fa-exclamation-circle"></i> 
                        <strong>{{ $stats['cuentas_vencidas'] }}</strong> cuentas vencidas.
                        <br>Total: <strong>${{ number_format($stats['total_pendiente'], 2) }}</strong>
                    </div>
                    <a href="{{ route('cxpagar.vencidas') }}" class="btn btn-danger btn-sm mt-2">
                        <i class="fas fa-eye"></i> Ver Cuentas
                    </a>
                @else
                    <p class="text-success mb-0">
                        <i class="fas fa-check-circle"></i> No hay cuentas vencidas.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Operaciones Recientes -->
<div class="row">
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-shopping-cart"></i> Operaciones
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('compras.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Compra
                    </a>
                    <a href="{{ route('traspasos.create') }}" class="btn btn-info">
                        <i class="fas fa-exchange-alt"></i> Nuevo Traspaso
                    </a>
                    <a href="{{ route('traspasos.por-recibir') }}" class="btn btn-warning">
                        <i class="fas fa-truck-loading"></i> Traspasos por Recibir
                        @if($stats['traspasos'] > 0)
                            <span class="badge bg-light text-dark">{{ $stats['traspasos'] }}</span>
                        @endif
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar"></i> Consultas Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('inventarios.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-boxes"></i> Consultar Inventarios
                    </a>
                    <a href="{{ route('cxpagar.index') }}" class="btn btn-outline-danger">
                        <i class="fas fa-money-bill-wave"></i> Cuentas por Pagar
                    </a>
                    <a href="{{ route('kardex.index') }}" class="btn btn-outline-info">
                        <i class="fas fa-clipboard-list"></i> Kardex
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
