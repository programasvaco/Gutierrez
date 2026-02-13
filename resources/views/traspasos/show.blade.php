@extends('layouts.app')

@section('title', 'Detalle del Traspaso')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('traspasos.index') }}">Traspasos</a></li>
                <li class="breadcrumb-item active">{{ $traspaso->folio }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-exchange-alt"></i> Traspaso {{ $traspaso->folio }}
                    <span class="badge {{ $traspaso->status_badge }} float-end">
                        <i class="{{ $traspaso->status_icon }}"></i> {{ ucfirst($traspaso->status) }}
                    </span>
                </h5>
            </div>
            <div class="card-body">
                <!-- Información General -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Información General</h6>
                        <p><strong>Fecha:</strong> {{ $traspaso->fecha->format('d/m/Y') }}</p>
                        <p><strong>Hora:</strong> {{ date('H:i:s', strtotime($traspaso->hora)) }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Almacenes</h6>
                        <p><strong>Origen:</strong> {{ $traspaso->almacenOrigen->nombre }}</p>
                        <p><strong>Destino:</strong> {{ $traspaso->almacenDestino->nombre }}</p>
                    </div>
                </div>

                <!-- Productos -->
                <h6 class="text-muted mb-3">Productos</h6>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Producto</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Costo</th>
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
                                <td class="text-end">${{ number_format($detalle->cantidad * $detalle->costo, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="4" class="text-end">TOTAL:</th>
                                <th class="text-end">
                                    ${{ number_format($traspaso->detalles->sum(function($d) { return $d->cantidad * $d->costo; }), 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Observaciones -->
                @if($traspaso->observaciones)
                <div class="mt-3">
                    <h6 class="text-muted">Observaciones</h6>
                    <p class="border p-2 bg-light">{{ $traspaso->observaciones }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Timeline -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-history"></i> Timeline</h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <i class="fas fa-file-alt text-secondary"></i>
                        <strong>Creado</strong><br>
                        <small>{{ $traspaso->created_at->format('d/m/Y H:i') }}</small>
                    </div>

                    @if($traspaso->fecha_transito)
                    <div class="timeline-item">
                        <i class="fas fa-truck text-warning"></i>
                        <strong>En Tránsito</strong><br>
                        <small>{{ $traspaso->fecha_transito->format('d/m/Y H:i') }}</small>
                    </div>
                    @endif

                    @if($traspaso->fecha_recepcion)
                    <div class="timeline-item">
                        <i class="fas fa-check-circle text-success"></i>
                        <strong>Recibido</strong><br>
                        <small>{{ $traspaso->fecha_recepcion->format('d/m/Y H:i') }}</small>
                    </div>
                    @endif

                    @if($traspaso->fecha_cancelacion)
                    <div class="timeline-item">
                        <i class="fas fa-times-circle text-danger"></i>
                        <strong>Cancelado</strong><br>
                        <small>{{ $traspaso->fecha_cancelacion->format('d/m/Y H:i') }}</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Botones de Impresión -->
        <div class="card mb-3 border-primary">
            <div class="card-header bg-primary text-white">
                <h6 class="mb-0"><i class="fas fa-print"></i> Imprimir Documentos</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <!-- Traspaso Completo -->
                    <a href="{{ route('traspasos.print', $traspaso) }}" class="btn btn-primary btn-sm" target="_blank">
                        <i class="fas fa-file-pdf"></i> Traspaso Completo
                    </a>

                    <!-- Remisión -->
                    <a href="{{ route('traspasos.print-remision', $traspaso) }}" class="btn btn-info btn-sm" target="_blank">
                        <i class="fas fa-file-invoice"></i> Remisión de Traslado
                    </a>

                    <!-- Orden de Salida -->
                    <a href="{{ route('traspasos.print-orden-salida', $traspaso) }}" class="btn btn-danger btn-sm" target="_blank">
                        <i class="fas fa-sign-out-alt"></i> Orden de Salida
                    </a>

                    <!-- Orden de Entrada -->
                    <a href="{{ route('traspasos.print-orden-entrada', $traspaso) }}" class="btn btn-success btn-sm" target="_blank">
                        <i class="fas fa-sign-in-alt"></i> Orden de Entrada
                    </a>
                </div>

                <hr>

                <p class="text-muted small mb-0">
                    <i class="fas fa-info-circle"></i> Los documentos se abrirán en una nueva ventana
                </p>
            </div>
        </div>

        <!-- Acciones -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-cog"></i> Acciones</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($traspaso->puedeCancelarse())
                        <form action="{{ route('traspasos.cancelar', $traspaso) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('¿Estás seguro de cancelar este traspaso?')">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </form>
                    @endif

                    @if($traspaso->puedePonerseEnTransito())
                        <form action="{{ route('traspasos.poner-en-transito', $traspaso) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning w-100" onclick="return confirm('¿Confirmas poner en tránsito este traspaso?')">
                                <i class="fas fa-truck"></i> Poner en Tránsito
                            </button>
                        </form>
                    @endif

                    @if($traspaso->puedeRecibirse())
                        <form action="{{ route('traspasos.recibir', $traspaso) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('¿Confirmas la recepción de este traspaso?')">
                                <i class="fas fa-check"></i> Recibir
                            </button>
                        </form>
                    @endif

                    <a href="{{ route('traspasos.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .timeline {
        position: relative;
        padding-left: 30px;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 20px;
        border-left: 2px solid #dee2e6;
        padding-left: 20px;
    }
    .timeline-item:last-child {
        border-left: none;
        padding-bottom: 0;
    }
    .timeline-item i {
        position: absolute;
        left: -9px;
        background: white;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
    }
</style>
@endpush
