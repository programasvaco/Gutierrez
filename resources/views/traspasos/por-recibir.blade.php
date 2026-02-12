@extends('layouts.app')

@section('title', 'Traspasos Por Recibir')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-truck-loading"></i> Traspasos Por Recibir</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-warning text-dark">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Traspasos en Tránsito</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('traspasos.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Lista
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtro por almacén destino -->
        <form action="{{ route('traspasos.por-recibir') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="almacen_destino_id" class="form-label">Filtrar por Almacén Destino:</label>
                    <select name="almacen_destino_id" class="form-select" onchange="this.form.submit()">
                        <option value="">Todos los almacenes</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_destino_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </form>

        @if($traspasos->count() > 0)
            @foreach($traspasos as $traspaso)
            <div class="card mb-3 border-warning">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-0">
                                <strong>{{ $traspaso->folio }}</strong> - {{ $traspaso->fecha->format('d/m/Y') }}
                                <span class="badge bg-warning text-dark ms-2">
                                    <i class="fas fa-truck"></i> En Tránsito
                                </span>
                            </h6>
                            <small class="text-muted">
                                Desde: <i class="fas fa-warehouse text-danger"></i> {{ $traspaso->almacenOrigen->nombre }}
                                → Hacia: <i class="fas fa-warehouse text-success"></i> {{ $traspaso->almacenDestino->nombre }}
                            </small>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('traspasos.show', $traspaso) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> Ver Detalle
                            </a>
                            <form action="{{ route('traspasos.recibir', $traspaso) }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('¿Confirma la recepción de este traspaso?')">
                                    <i class="fas fa-check"></i> Recibir
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Producto</th>
                                    <th class="text-end">Cantidad</th>
                                    <th class="text-end">Costo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($traspaso->detalles as $detalle)
                                <tr>
                                    <td><code>{{ $detalle->producto->codigo }}</code></td>
                                    <td>{{ $detalle->producto->descripcion }}</td>
                                    <td class="text-end">{{ number_format($detalle->cantidad, 2) }}</td>
                                    <td class="text-end">${{ number_format($detalle->costo, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="2">Total de productos:</th>
                                    <th class="text-end">{{ number_format($traspaso->detalles->sum('cantidad'), 2) }}</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    @if($traspaso->observaciones)
                    <div class="alert alert-info mt-2 mb-0">
                        <strong>Observaciones:</strong> {{ $traspaso->observaciones }}
                    </div>
                    @endif

                    <div class="text-muted mt-2">
                        <small>
                            <i class="fas fa-clock"></i> En tránsito desde: {{ $traspaso->fecha_transito->format('d/m/Y H:i:s') }}
                        </small>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="alert alert-success text-center">
                <i class="fas fa-check-circle"></i> No hay traspasos pendientes de recibir.
            </div>
        @endif
    </div>
</div>
@endsection
