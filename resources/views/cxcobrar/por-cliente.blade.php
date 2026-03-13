@extends('layouts.app')

@section('title', 'CxC por Cliente')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-user"></i> Cuentas por Cobrar por Cliente</h2>
    </div>
</div>

<div class="mb-3">
    <a href="{{ route('cxcobrar.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="fas fa-arrow-left"></i> Todas las Cuentas
    </a>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form action="{{ route('cxcobrar.por-cliente') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label">Seleccione un Cliente</label>
                    <select name="cliente_id" class="form-select" required>
                        <option value="">— Seleccione —</option>
                        @foreach($clientes as $cliente)
                            <option value="{{ $cliente->id }}" {{ isset($cliente_id) && $cliente_id == $cliente->id ? 'selected' : '' }}>
                                {{ $cliente->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Ver
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@isset($cliente)
<div class="row mb-3">
    <div class="col-md-4">
        <div class="card border-primary">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Importe Total</h6>
                <h4 class="text-primary mb-0">${{ number_format($totalImporte, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-warning">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Saldo Pendiente</h6>
                <h4 class="text-warning mb-0">${{ number_format($totalSaldo, 2) }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-success">
            <div class="card-body text-center">
                <h6 class="text-muted mb-1">Cobrado</h6>
                <h4 class="text-success mb-0">${{ number_format($totalImporte - $totalSaldo, 2) }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Cuenta de: <strong>{{ $cliente->nombre }}</strong></h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Fecha</th>
                        <th>Vence</th>
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
                        <td colspan="6" class="text-center text-muted py-4">Sin registros para este cliente.</td>
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
@endisset
@endsection
