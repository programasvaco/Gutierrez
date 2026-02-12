@extends('layouts.app')

@section('title', 'Detalles del Traspaso')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('traspasos.index') }}">Traspasos</a></li>
                <li class="breadcrumb-item active">Detalles</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Información General -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="mb-0"><i class="fas fa-exchange-alt"></i> Traspaso {{ $traspaso->folio }}</h5>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge {{ $traspaso->status_badge }} fs-6">
                            <i class="fas {{ $traspaso->status_icon }}"></i> {{ ucfirst($traspaso->status) }}
                        </span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-calendar"></i> Fecha:</label>
                            <p class="fw-bold">{{ $traspaso->fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-clock"></i> Hora:</label>
                            <p class="fw-bold">{{ date('H:i:s', strtotime($traspaso->hora)) }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-warehouse text-danger"></i> Origen:</label>
                            <p class="fw-bold">{{ $traspaso->almacenOrigen->nombre }}</p>
                            <small class="text-muted">{{ $traspaso->almacenOrigen->ciudad }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-warehouse text-success"></i> Destino:</label>
                            <p class="fw-bold">{{ $traspaso->almacenDestino->nombre }}</p>
                            <small class="text-muted">{{ $traspaso->almacenDestino->ciudad }}</small>
                        </div>
                    </div>
                </div>

                @if($traspaso->observaciones)
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <strong><i class="fas fa-sticky-note"></i> Observaciones:</strong><br>
                            {{ $traspaso->observaciones }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Productos -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box"></i> Productos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Costo Unit.</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($traspaso->detalles as $detalle)
                            <tr>
                                <td><code>{{ $detalle->producto->codigo }}</code></td>
                                <td>{{ $detalle->producto->descripcion }}</td>
                                <td class="text-end">{{ number_format($detalle->cantidad, 2) }}</td>
                                <td class="text-end">${{ number_format($detalle->costo, 2) }}</td>
                                <td class="text-end"><strong>${{ number_format($detalle->cantidad * $detalle->costo, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="2">TOTALES:</th>
                                <th class="text-end">{{ number_format($traspaso->detalles->sum('cantidad'), 2) }}</th>
                                <th></th>
                                <th class="text-end"><strong>${{ number_format($traspaso->detalles->sum(function($d) { return $d->cantidad * $d->costo; }), 2) }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Timeline de Estados -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-history"></i> Historial de Estados</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i> <strong>Creado</strong>
                        <br><small class="text-muted">{{ $traspaso->created_at->format('d/m/Y H:i:s') }}</small>
                    </li>
                    
                    @if($traspaso->fecha_transito)
                    <li class="list-group-item">
                        <i class="fas fa-truck text-warning"></i> <strong>En Tránsito</strong>
                        <br><small class="text-muted">{{ $traspaso->fecha_transito->format('d/m/Y H:i:s') }}</small>
                    </li>
                    @endif

                    @if($traspaso->fecha_recepcion)
                    <li class="list-group-item">
                        <i class="fas fa-check-circle text-success"></i> <strong>Recibido</strong>
                        <br><small class="text-muted">{{ $traspaso->fecha_recepcion->format('d/m/Y H:i:s') }}</small>
                    </li>
                    @endif

                    @if($traspaso->fecha_cancelacion)
                    <li class="list-group-item">
                        <i class="fas fa-times-circle text-danger"></i> <strong>Cancelado</strong>
                        <br><small class="text-muted">{{ $traspaso->fecha_cancelacion->format('d/m/Y H:i:s') }}</small>
                    </li>
                    @endif
                </ul>
            </div>
        </div>
    </div>

    <!-- Acciones Disponibles -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-tasks"></i> Acciones Disponibles</h5>
            </div>
            <div class="card-body">
                @if($traspaso->puedePonerseEnTransito())
                    <form action="{{ route('traspasos.poner-en-transito', $traspaso) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-warning w-100" onclick="return confirm('¿Confirma poner este traspaso en tránsito?\n\nEsto realizará las salidas del almacén origen.')">
                            <i class="fas fa-truck"></i> Poner en Tránsito
                        </button>
                    </form>
                    <small class="text-muted d-block mb-3">
                        <i class="fas fa-info-circle"></i> Al poner en tránsito se descontará del inventario del almacén origen.
                    </small>
                @endif

                @if($traspaso->puedeRecibirse())
                    <form action="{{ route('traspasos.recibir', $traspaso) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Confirma la recepción de este traspaso?\n\nEsto incrementará el inventario del almacén destino.')">
                            <i class="fas fa-check"></i> Recibir Traspaso
                        </button>
                    </form>
                    <small class="text-muted d-block mb-3">
                        <i class="fas fa-info-circle"></i> Al recibir se incrementará el inventario del almacén destino.
                    </small>
                @endif

                @if($traspaso->puedeCancelarse())
                    <form action="{{ route('traspasos.cancelar', $traspaso) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Está seguro de cancelar este traspaso?\n\nEsta acción no se puede deshacer.')">
                            <i class="fas fa-times"></i> Cancelar Traspaso
                        </button>
                    </form>
                    <small class="text-muted d-block mb-3">
                        <i class="fas fa-exclamation-triangle"></i> Solo se pueden cancelar traspasos en estado "creado".
                    </small>
                @endif

                @if($traspaso->status == 'recibido')
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i> Este traspaso ha sido completado exitosamente.
                    </div>
                @endif

                @if($traspaso->status == 'cancelado')
                    <div class="alert alert-danger">
                        <i class="fas fa-times-circle"></i> Este traspaso ha sido cancelado.
                    </div>
                @endif

                @if($traspaso->status == 'en transito')
                    <div class="alert alert-warning">
                        <i class="fas fa-truck"></i> Este traspaso está en tránsito. No se puede cancelar.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<hr>

<div class="d-flex justify-content-between">
    <a href="{{ route('traspasos.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>
    
    @if(in_array($traspaso->status, ['creado', 'cancelado']))
    <form action="{{ route('traspasos.destroy', $traspaso) }}" method="POST" style="display: inline;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Está seguro de eliminar este traspaso?')">
            <i class="fas fa-trash"></i> Eliminar
        </button>
    </form>
    @endif
</div>
@endsection

@push('styles')
<style>
    .info-item {
        margin-bottom: 0.5rem;
    }
    .info-item label {
        display: block;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    .info-item p {
        margin-bottom: 0;
        font-size: 1rem;
    }
</style>
@endpush
