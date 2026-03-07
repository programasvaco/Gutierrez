@extends('layouts.app')

@section('title', 'Flujo de Caja')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-money-bill-wave"></i> Flujo de Caja</h2>
    </div>
</div>

<!-- Resumen -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Total Entradas</h6>
                <h4 class="text-success mb-0">${{ number_format($totalEntradas, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Total Salidas</h6>
                <h4 class="text-danger mb-0">${{ number_format($totalSalidas, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card {{ $saldo >= 0 ? 'border-primary' : 'border-warning' }}">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Saldo</h6>
                <h4 class="{{ $saldo >= 0 ? 'text-primary' : 'text-warning' }} mb-0">${{ number_format($saldo, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Movimientos</h5>
    </div>
    <div class="card-body">

        <!-- Filtros -->
        <form action="{{ route('flujo-caja.index') }}" method="GET" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="tipo" class="form-select">
                        <option value="">Todos los tipos</option>
                        <option value="Entrada" {{ request('tipo') === 'Entrada' ? 'selected' : '' }}>Entrada</option>
                        <option value="Salida"  {{ request('tipo') === 'Salida'  ? 'selected' : '' }}>Salida</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="almacen_id" class="form-select">
                        <option value="">Todos los almacenes</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" placeholder="Desde">
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('flujo-caja.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Tipo</th>
                        <th>Referencia</th>
                        <th>Almacén</th>
                        <th class="text-end">Cantidad</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($registros as $registro)
                    <tr>
                        <td>{{ $registro->fecha->format('d/m/Y') }}</td>
                        <td>
                            @if($registro->tipo === 'Entrada')
                                <span class="badge bg-success">
                                    <i class="fas fa-arrow-down"></i> Entrada
                                </span>
                            @else
                                <span class="badge bg-danger">
                                    <i class="fas fa-arrow-up"></i> Salida
                                </span>
                            @endif
                        </td>
                        <td><code>{{ $registro->referencia }}</code></td>
                        <td>{{ $registro->almacen->nombre }}</td>
                        <td class="text-end fw-bold {{ $registro->tipo === 'Entrada' ? 'text-success' : 'text-danger' }}">
                            {{ $registro->tipo === 'Entrada' ? '+' : '-' }}${{ number_format($registro->cantidad, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No hay movimientos registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $registros->links() }}
        </div>

    </div>
</div>
@endsection
