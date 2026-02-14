@extends('layouts.app')

@section('title', 'Detalles del Producto')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active">Detalles del Producto</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Detalles del Producto</h5>
                <span class="badge badge-{{ $producto->status }} fs-6">
                    {{ ucfirst($producto->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Imagen del producto -->
                    <div class="col-md-4 mb-4">
                        @if($producto->imagen)
                            <img src="{{ asset('storage/productos/' . $producto->imagen) }}" alt="{{ $producto->descripcion }}" class="img-fluid rounded shadow-sm">
                        @else
                            <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="height: 300px;">
                                <div class="text-center">
                                    <i class="fas fa-image fa-5x mb-3"></i>
                                    <p>Sin imagen</p>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Información del producto -->
                    <div class="col-md-8">
                        <h3 class="mb-4">{{ $producto->descripcion }}</h3>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-barcode"></i> Código:</label>
                                    <p class="fw-bold">{{ $producto->codigo }}</p>
                                </div>
                            </div>
                            @if($producto->codigo_empaque)
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-box"></i> Código de Empaque:</label>
                                    <p class="fw-bold">{{ $producto->codigo_empaque }}</p>
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-ruler"></i> Unidad:</label>
                                    <p class="fw-bold">{{ $producto->unidad }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-shopping-cart"></i> Unidad de Compra:</label>
                                    <p class="fw-bold">{{ $producto->unidad_compra }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-balance-scale"></i> Contenido:</label>
                                    <p class="fw-bold">{{ $producto->contenido }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-arrow-down"></i> Stock Mínimo:</label>
                                    <p><span class="badge bg-info fs-6">{{ $producto->stock_min }}</span></p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-arrow-up"></i> Stock Máximo:</label>
                                    <p><span class="badge bg-warning fs-6">{{ $producto->stock_max }}</span></p>
                                </div>
                            </div>
                        </div>

                        {{-- precios --}}
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-dollar-sign"></i> Precio venta:</label>
                                    <p class="fw-bold">{{ $producto->precio_venta_formateado }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-arrow-down-wide-short"></i> Precio mínimo:</label>
                                    <p class="fw-bold">{{ $producto->precio_minimo_formateado }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-calendar-alt"></i> Fecha de Creación:</label>
                                    <p>{{ $producto->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="info-item">
                                    <label class="text-muted"><i class="fas fa-calendar-check"></i> Última Actualización:</label>
                                    <p>{{ $producto->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <div>
                        <a href="{{ route('productos.edit', $producto) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>

                <form id="delete-form" action="{{ route('productos.destroy', $producto) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .info-item {
        margin-bottom: 1rem;
    }
    .info-item label {
        display: block;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    .info-item p {
        margin-bottom: 0;
        font-size: 1.1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Está seguro de que desea eliminar este producto?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
