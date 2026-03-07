@extends('layouts.app')

@section('title', 'CxC Vencidas')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-exclamation-triangle text-danger"></i> Cuentas por Cobrar Vencidas</h2>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card border-danger">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Total Vencido</h6>
                <h4 class="text-danger mb-0">${{ number_format($totalVencido, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="mb-3">
    <a href="{{ route('cxcobrar.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Todas las Cuentas
    </a>
</div>

<div class="card">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">Cuentas Vencidas</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('cxcobrar.vencidas') }}" method="GET" class="mb-3">
            <div class="row g-2">
                <div class="col-md-4">
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
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Vence</th>
                        <th>Días Vencida</th>
                        <th>Cliente</th>
                        <th>Folio Venta</th>
                        <th class="text-end">Importe</th>
                        <th class="text-end">Saldo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($cuentas as $cuenta)
                    <tr class="table-danger">
                        <td>{{ $cuenta->fecha->format('d/m/Y') }}</td>
                        <td>{{ $cuenta->fecha_vencimiento->format('d/m/Y') }}</td>
                        <td class="fw-bold text-danger">{{ now()->diffInDays($cuenta->fecha_vencimiento) }} días</td>
                        <td>{{ $cuenta->cliente->nombre }}</td>
                        <td><code>{{ $cuenta->venta->folio }}</code></td>
                        <td class="text-end">${{ number_format($cuenta->importe, 2) }}</td>
                        <td class="text-end fw-bold">${{ number_format($cuenta->saldo, 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            <i class="fas fa-check-circle fa-2x mb-2 d-block text-success"></i>
                            No hay cuentas vencidas. ¡Todo al corriente!
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
