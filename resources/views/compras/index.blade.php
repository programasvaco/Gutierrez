@extends('layouts.app')

@section('title', 'Lista de Compras')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-shopping-cart"></i> Gestión de Compras</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Lista de Compras</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('compras.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Compra
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros y búsqueda -->
        <form action="{{ route('compras.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por referencia o proveedor..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_desde" class="form-control" placeholder="Desde" value="{{ request('fecha_desde') }}">
                </div>
                <div class="col-md-3">
                    <input type="date" name="fecha_hasta" class="form-control" placeholder="Hasta" value="{{ request('fecha_hasta') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>

        @if($compras->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Referencia</th>
                            <th>Proveedor</th>
                            <th>Almacén</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end">Impuestos</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($compras as $compra)
                        <tr>
                            <td>{{ $compra->fecha->format('d/m/Y') }}</td>
                            <td><strong>{{ $compra->referencia }}</strong></td>
                            <td>{{ $compra->proveedor->nombre }}</td>
                            <td>
                                <i class="fas fa-warehouse text-primary"></i> {{ $compra->almacen->nombre }}
                            </td>
                            <td class="text-end">${{ number_format($compra->subtotal, 2) }}</td>
                            <td class="text-end">${{ number_format($compra->impuestos, 2) }}</td>
                            <td class="text-end"><strong>${{ number_format($compra->total, 2) }}</strong></td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('compras.show', $compra) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion({{ $compra->id }})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $compra->id }}" action="{{ route('compras.destroy', $compra) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">TOTALES:</th>
                            <th class="text-end">${{ number_format($compras->sum('subtotal'), 2) }}</th>
                            <th class="text-end">${{ number_format($compras->sum('impuestos'), 2) }}</th>
                            <th class="text-end"><strong>${{ number_format($compras->sum('total'), 2) }}</strong></th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $compras->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron compras.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Está seguro de que desea eliminar esta compra?\n\nEsto revertirá los movimientos de inventario y cuentas por pagar.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
