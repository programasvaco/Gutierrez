@extends('layouts.app')

@section('title', 'Cuentas por Cobrar')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-hand-holding-usd"></i> Cuentas por Cobrar</h2>
    </div>
</div>

<!-- Resumen -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Total Pendiente</h6>
                <h4 class="text-primary mb-0">${{ number_format($totalPendiente, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Total Vencido</h6>
                <h4 class="text-danger mb-0">${{ number_format($totalVencido, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Cuentas Vencidas</h6>
                <h4 class="text-warning mb-0">{{ $cantidadVencidas }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Accesos rápidos -->
<div class="mb-3 d-flex gap-2">
    <a href="{{ route('cxcobrar.vencidas') }}" class="btn btn-outline-danger btn-sm">
        <i class="fas fa-exclamation-triangle"></i> Ver Vencidas
    </a>
    <a href="{{ route('cxcobrar.por-cliente') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-user"></i> Por Cliente
    </a>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Listado</h5>
    </div>
    <div class="card-body">

        <!-- Filtros -->
        <form action="{{ route('cxcobrar.index') }}" method="GET" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <select name="cliente_id" class="form-select">
                        <option value="">Todos los clientes</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ request('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="estado" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="pendientes" {{ request('estado') === 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                        <option value="vencidas"   {{ request('estado') === 'vencidas'   ? 'selected' : '' }}>Vencidas</option>
                        <option value="cobradas"   {{ request('estado') === 'cobradas'   ? 'selected' : '' }}>Cobradas</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" placeholder="Desde">
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-md-3 d-flex gap-1">
                    <button type="submit" class="btn btn-primary flex-fill">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('cxcobrar.index') }}" class="btn btn-outline-secondary">
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
                        <th>Vence</th>
                        <th>Cliente</th>
                        <th>Folio Venta</th>
                        <th class="text-end">Importe</th>
                        <th class="text-end">Saldo</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cuentas as $cuenta)
                    <tr class="{{ $cuenta->vencida ? 'table-danger' : '' }}">
                        <td>{{ $cuenta->fecha->format('d/m/Y') }}</td>
                        <td>{{ $cuenta->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td>{{ $cuenta->cliente->nombre }}</td>
                        <td><code>{{ $cuenta->venta->folio }}</code></td>
                        <td class="text-end">${{ number_format($cuenta->importe, 2) }}</td>
                        <td class="text-end fw-bold">${{ number_format($cuenta->saldo, 2) }}</td>
                        <td class="text-center">
                            @if($cuenta->cobrada)
                                <span class="badge bg-success">Cobrada</span>
                            @elseif($cuenta->vencida)
                                <span class="badge bg-danger">Vencida</span>
                            @else
                                <span class="badge bg-warning text-dark">Pendiente</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            No hay cuentas por cobrar registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $cuentas->links() }}
        </div>

    </div>
</div>
@endsection
