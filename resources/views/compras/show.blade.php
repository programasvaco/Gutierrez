@extends('layouts.app')

@section('title', 'Detalles de la Compra')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
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
                <h5 class="mb-0"><i class="fas fa-file-invoice"></i> Compra #{{ $compra->id }} - {{ $compra->referencia }}</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-calendar"></i> Fecha:</label>
                            <p class="fw-bold">{{ $compra->fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-hashtag"></i> Referencia:</label>
                            <p class="fw-bold">{{ $compra->referencia }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-truck"></i> Proveedor:</label>
                            <p class="fw-bold">{{ $compra->proveedor->nombre }}</p>
                            <small class="text-muted">{{ $compra->proveedor->razon_social }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-warehouse"></i> Almacén:</label>
                            <p class="fw-bold">{{ $compra->almacen->nombre }}</p>
                            <small class="text-muted">{{ $compra->almacen->ciudad }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalles de Productos -->
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
                                <th class="text-end">Impuestos</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($compra->detalles as $detalle)
                            <tr>
                                <td><code>{{ $detalle->producto->codigo }}</code></td>
                                <td>{{ $detalle->producto->descripcion }}</td>
                                <td class="text-end">{{ number_format($detalle->cantidad, 2) }}</td>
                                <td class="text-end">${{ number_format($detalle->costo, 2) }}</td>
                                <td class="text-end">${{ number_format($detalle->impuestos, 2) }}</td>
                                <td class="text-end"><strong>${{ number_format($detalle->subtotal, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Subtotal:</th>
                                <th class="text-end">${{ number_format($compra->subtotal, 2) }}</th>
                            </tr>
                            <tr>
                                <th colspan="5" class="text-end">Impuestos:</th>
                                <th class="text-end">${{ number_format($compra->impuestos, 2) }}</th>
                            </tr>
                            <tr class="table-primary">
                                <th colspan="5" class="text-end">TOTAL:</th>
                                <th class="text-end"><strong>${{ number_format($compra->total, 2) }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Cuenta por Pagar -->
    @if($compra->cuentaPorPagar)
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> Cuenta por Pagar</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="text-muted"><i class="fas fa-calendar-alt"></i> Fecha:</label>
                            <p>{{ $compra->cuentaPorPagar->fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="text-muted"><i class="fas fa-calendar-check"></i> Vencimiento:</label>
                            <p>{{ $compra->cuentaPorPagar->fecha_vencimiento->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="text-muted"><i class="fas fa-dollar-sign"></i> Importe:</label>
                            <p class="fw-bold">${{ number_format($compra->cuentaPorPagar->importe, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <label class="text-muted"><i class="fas fa-balance-scale"></i> Saldo:</label>
                            <p class="fw-bold text-danger">${{ number_format($compra->cuentaPorPagar->saldo, 2) }}</p>
                        </div>
                    </div>
                    <div class="col-md-12">
                        @if($compra->cuentaPorPagar->pagada)
                            <span class="badge bg-success fs-6"><i class="fas fa-check-circle"></i> Pagada</span>
                        @elseif($compra->cuentaPorPagar->vencida)
                            <span class="badge bg-danger fs-6"><i class="fas fa-exclamation-triangle"></i> Vencida</span>
                        @else
                            <span class="badge bg-warning fs-6"><i class="fas fa-clock"></i> Pendiente</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Información del Sistema -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información del Sistema</h5>
            </div>
            <div class="card-body">
                <div class="info-item mb-3">
                    <label class="text-muted"><i class="fas fa-hashtag"></i> ID de Compra:</label>
                    <p>#{{ $compra->id }}</p>
                </div>
                <div class="info-item mb-3">
                    <label class="text-muted"><i class="fas fa-calendar-plus"></i> Fecha de Creación:</label>
                    <p>{{ $compra->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
                <div class="info-item">
                    <label class="text-muted"><i class="fas fa-calendar-check"></i> Última Actualización:</label>
                    <p>{{ $compra->updated_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="d-flex justify-content-between">
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>
    <div>
        <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
            <i class="fas fa-trash"></i> Eliminar Compra
        </button>
    </div>
</div>

<form id="delete-form" action="{{ route('compras.destroy', $compra) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
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

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Está seguro de que desea eliminar esta compra?\n\nEsto revertirá:\n- Los movimientos de inventario\n- La cuenta por pagar\n- Todos los detalles de la compra')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
