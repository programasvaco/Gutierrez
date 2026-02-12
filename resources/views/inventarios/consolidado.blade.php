@extends('layouts.app')

@section('title', 'Inventario Consolidado')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-chart-bar"></i> Inventario Consolidado por Producto</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Existencias Totales por Producto</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('inventarios.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Búsqueda -->
        <form action="{{ route('inventarios.consolidado') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-10">
                    <label class="form-label">Búsqueda</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por código o descripción..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
            </div>
        </form>

        @if($productos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th class="text-end">Existencia Total</th>
                            <th class="text-center">Almacenes</th>
                            <th class="text-end">Stock Min</th>
                            <th class="text-end">Stock Max</th>
                            <th class="text-center">Estado General</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr>
                            <td><code>{{ $producto->codigo }}</code></td>
                            <td>
                                <strong>{{ $producto->descripcion }}</strong>
                                <br><small class="text-muted">{{ $producto->unidad }}</small>
                            </td>
                            <td class="text-end">
                                <strong class="fs-4 text-primary">{{ number_format($producto->existencia_total, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $producto->almacenes_con_stock }} almacenes</span>
                            </td>
                            <td class="text-end">{{ number_format($producto->stock_min, 2) }}</td>
                            <td class="text-end">{{ number_format($producto->stock_max, 2) }}</td>
                            <td class="text-center">
                                @if($producto->existencia_total <= 0)
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Sin Stock</span>
                                @elseif($producto->existencia_total < $producto->stock_min)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Bajo</span>
                                @elseif($producto->existencia_total > $producto->stock_max)
                                    <span class="badge bg-info"><i class="fas fa-arrow-up"></i> Exceso</span>
                                @else
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Normal</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2" class="text-end">TOTAL GENERAL:</th>
                            <th class="text-end">{{ number_format($productos->sum('existencia_total'), 2) }}</th>
                            <th colspan="4"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Mostrando {{ $productos->firstItem() }} - {{ $productos->lastItem() }} de {{ $productos->total() }} productos
                </div>
                <div>
                    {{ $productos->links() }}
                </div>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron productos con los criterios de búsqueda.
            </div>
        @endif
    </div>
</div>
@endsection
