@extends('layouts.app')

@section('title', 'Consulta de Inventarios')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-boxes"></i> Consulta de Inventarios</h2>
    </div>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-database"></i> Total Registros</h6>
                <h2 class="mb-0">{{ number_format($totalRegistros) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-check-circle"></i> Con Stock</h6>
                <h2 class="mb-0">{{ number_format($conStock) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-times-circle"></i> Sin Stock</h6>
                <h2 class="mb-0">{{ number_format($sinStock) }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Existencias por Almacén</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('inventarios.stock-bajo') }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-exclamation-triangle"></i> Stock Bajo
                </a>
                <a href="{{ route('inventarios.consolidado') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-chart-bar"></i> Consolidado
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <form action="{{ route('inventarios.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <!-- Búsqueda -->
                <div class="col-md-3">
                    <label class="form-label">Búsqueda</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Código o descripción..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Filtro por Producto -->
                <div class="col-md-3">
                    <label class="form-label">Producto</label>
                    <select name="producto_id" class="form-select">
                        <option value="">Todos los productos</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->codigo }} - {{ Str::limit($producto->descripcion, 30) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Filtro por Almacén -->
                <div class="col-md-2">
                    <label class="form-label">Almacén</label>
                    <select name="almacen_id" class="form-select">
                        <option value="">Todos</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Tipo de Existencia -->
                <div class="col-md-2">
                    <label class="form-label">Existencia</label>
                    <select name="tipo_existencia" class="form-select">
                        <option value="">Todos</option>
                        <option value="con_stock" {{ request('tipo_existencia') == 'con_stock' ? 'selected' : '' }}>Con Stock</option>
                        <option value="sin_stock" {{ request('tipo_existencia') == 'sin_stock' ? 'selected' : '' }}>Sin Stock</option>
                        <option value="stock_bajo" {{ request('tipo_existencia') == 'stock_bajo' ? 'selected' : '' }}>Stock Bajo</option>
                    </select>
                </div>

                <!-- Ordenar por -->
                <div class="col-md-2">
                    <label class="form-label">Ordenar por</label>
                    <select name="order_by" class="form-select">
                        <option value="producto" {{ request('order_by') == 'producto' ? 'selected' : '' }}>Producto</option>
                        <option value="almacen" {{ request('order_by') == 'almacen' ? 'selected' : '' }}>Almacén</option>
                        <option value="existencia" {{ request('order_by') == 'existencia' ? 'selected' : '' }}>Existencia</option>
                    </select>
                    <input type="hidden" name="order_direction" value="{{ request('order_direction', 'asc') }}">
                </div>

                <!-- Botones -->
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                    
                    <!-- Toggle de dirección de orden -->
                    @if(request('order_by'))
                        <a href="{{ route('inventarios.index', array_merge(request()->query(), ['order_direction' => request('order_direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                           class="btn btn-outline-primary">
                            <i class="fas fa-sort"></i> 
                            {{ request('order_direction') == 'asc' ? 'Ascendente' : 'Descendente' }}
                        </a>
                    @endif
                </div>
            </div>
        </form>

        @if($inventarios->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <th class="text-end">Existencia</th>
                            <th class="text-end">Stock Min</th>
                            <th class="text-end">Stock Max</th>
                            <th class="text-center">Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventarios as $inventario)
                        @php
                            $rowClass = '';
                            if ($inventario->existencia <= 0) {
                                $rowClass = 'table-danger';
                            } elseif ($inventario->existencia < $inventario->producto->stock_min) {
                                $rowClass = 'table-warning';
                            }
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td><code>{{ $inventario->producto->codigo }}</code></td>
                            <td>
                                <strong>{{ $inventario->producto->descripcion }}</strong>
                                <br><small class="text-muted">{{ $inventario->producto->unidad }}</small>
                            </td>
                            <td>
                                <i class="fas fa-warehouse text-primary"></i> {{ $inventario->almacen->nombre }}
                                <br><small class="text-muted">{{ $inventario->almacen->ciudad }}</small>
                            </td>
                            <td class="text-end">
                                <strong class="fs-5">{{ number_format($inventario->existencia, 2) }}</strong>
                            </td>
                            <td class="text-end">{{ number_format($inventario->producto->stock_min, 2) }}</td>
                            <td class="text-end">{{ number_format($inventario->producto->stock_max, 2) }}</td>
                            <td class="text-center">
                                @if($inventario->existencia <= 0)
                                    <span class="badge bg-danger"><i class="fas fa-times"></i> Sin Stock</span>
                                @elseif($inventario->existencia < $inventario->producto->stock_min)
                                    <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Bajo</span>
                                @elseif($inventario->existencia > $inventario->producto->stock_max)
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
                            <th colspan="3" class="text-end">TOTALES EN PÁGINA:</th>
                            <th class="text-end">{{ number_format($inventarios->sum('existencia'), 2) }}</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Mostrando {{ $inventarios->firstItem() }} - {{ $inventarios->lastItem() }} de {{ $inventarios->total() }} registros
                </div>
                <div>
                    {{ $inventarios->links() }}
                </div>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron registros de inventario con los filtros seleccionados.
            </div>
        @endif
    </div>
</div>
@endsection
