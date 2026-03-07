@extends('layouts.app')

@section('title', 'Venta {{ $venta->folio }}')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">{{ $venta->folio }}</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <!-- Encabezado de la venta -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Venta {{ $venta->folio }}</h5>
                <span class="fs-6">{{ $venta->fecha->format('d/m/Y') }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-hashtag"></i> Folio:</label>
                            <p class="fw-bold"><code>{{ $venta->folio }}</code></p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-calendar"></i> Fecha:</label>
                            <p class="fw-bold">{{ $venta->fecha->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-warehouse"></i> Almacén:</label>
                            <p class="fw-bold">{{ $venta->almacen->nombre }}</p>
                            <small class="text-muted">{{ $venta->almacen->ciudad }}</small>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-credit-card"></i> Tipo de Pago:</label>
                            <p class="fw-bold">
                                @if($venta->tipo_pago === 'contado')
                                    <span class="badge bg-success">Contado</span>
                                @else
                                    <span class="badge bg-warning text-dark">Crédito</span>
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="info-item">
                            <label class="text-muted"><i class="fas fa-user"></i> Cliente:</label>
                            @if($venta->cliente)
                                <p class="fw-bold">{{ $venta->cliente->nombre }}</p>
                                @if($venta->cliente->razon_social)
                                    <small class="text-muted">{{ $venta->cliente->razon_social }}</small>
                                @endif
                            @else
                                <p class="text-muted"><i class="fas fa-store"></i> Venta de mostrador</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalle de artículos -->
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-box"></i> Artículos Vendidos</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Precio Unit.</th>
                                <th class="text-end">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $detalle)
                            <tr>
                                <td><code>{{ $detalle->producto->codigo }}</code></td>
                                <td>{{ $detalle->producto->descripcion }}</td>
                                <td>{{ $detalle->producto->unidad }}</td>
                                <td class="text-end">{{ number_format($detalle->cantidad, 2) }}</td>
                                <td class="text-end">${{ number_format($detalle->precio, 2) }}</td>
                                <td class="text-end"><strong>${{ number_format($detalle->subtotal, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Subtotal:</th>
                                <th class="text-end">${{ number_format($venta->subtotal, 2) }}</th>
                            </tr>
                            <tr class="table-success">
                                <th colspan="5" class="text-end fs-5">TOTAL:</th>
                                <th class="text-end fs-5">${{ number_format($venta->total, 2) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Información del sistema -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información del Sistema</h6>
            </div>
            <div class="card-body">
                <div class="info-item mb-2">
                    <label class="text-muted"><i class="fas fa-hashtag"></i> ID:</label>
                    <p>#{{ $venta->id }}</p>
                </div>
                <div class="info-item mb-2">
                    <label class="text-muted"><i class="fas fa-calendar-plus"></i> Registrada:</label>
                    <p>{{ $venta->created_at->format('d/m/Y H:i:s') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="d-flex justify-content-between">
    <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Volver al Listado
    </a>
    <button type="button" class="btn btn-danger" onclick="confirmarCancelacion()">
        <i class="fas fa-times-circle"></i> Cancelar Venta
    </button>
</div>

<form id="delete-form" action="{{ route('ventas.destroy', $venta) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('styles')
<style>
    .info-item label {
        display: block;
        font-size: 0.85rem;
        margin-bottom: 0.15rem;
    }
    .info-item p {
        margin-bottom: 0;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarCancelacion() {
        if (confirm('¿Cancelar la venta {{ $venta->folio }}?\n\nEsto restaurará las existencias en el almacén «{{ $venta->almacen->nombre }}».')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
