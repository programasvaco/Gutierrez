@extends('layouts.app')

@section('title', 'Productos con Stock Bajo')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-exclamation-triangle text-warning"></i> Productos con Stock Bajo</h2>
    </div>
</div>

<div class="card border-warning">
    <div class="card-header bg-warning text-dark">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Productos debajo del Stock Mínimo</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('inventarios.index') }}" class="btn btn-dark btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtro -->
        <form action="{{ route('inventarios.stock-bajo') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-10">
                    <label class="form-label">Filtrar por Almacén</label>
                    <select name="almacen_id" class="form-select">
                        <option value="">Todos los almacenes</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>

        @if($inventarios->count() > 0)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Se encontraron <strong>{{ $inventarios->total() }}</strong> productos con stock bajo.
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Código</th>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <th class="text-end">Existencia</th>
                            <th class="text-end">Stock Min</th>
                            <th class="text-end">Faltante</th>
                            <th class="text-center">Urgencia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($inventarios as $inventario)
                        @php
                            $faltante = $inventario->producto->stock_min - $inventario->existencia;
                            $porcentaje = $inventario->producto->stock_min > 0 
                                ? ($inventario->existencia / $inventario->producto->stock_min) * 100 
                                : 0;
                        @endphp
                        <tr class="table-warning">
                            <td><code>{{ $inventario->producto->codigo }}</code></td>
                            <td>
                                <strong>{{ $inventario->producto->descripcion }}</strong>
                                <br><small class="text-muted">{{ $inventario->producto->unidad }}</small>
                            </td>
                            <td>
                                <i class="fas fa-warehouse text-primary"></i> {{ $inventario->almacen->nombre }}
                            </td>
                            <td class="text-end">
                                <strong class="text-danger fs-5">{{ number_format($inventario->existencia, 2) }}</strong>
                            </td>
                            <td class="text-end">{{ number_format($inventario->producto->stock_min, 2) }}</td>
                            <td class="text-end">
                                <span class="badge bg-danger">{{ number_format($faltante, 2) }}</span>
                            </td>
                            <td class="text-center">
                                @if($porcentaje <= 25)
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-exclamation-circle"></i> CRÍTICO
                                    </span>
                                @elseif($porcentaje <= 50)
                                    <span class="badge bg-warning text-dark fs-6">
                                        <i class="fas fa-exclamation-triangle"></i> URGENTE
                                    </span>
                                @else
                                    <span class="badge bg-info fs-6">
                                        <i class="fas fa-info-circle"></i> ATENCIÓN
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
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
            <div class="alert alert-success text-center">
                <i class="fas fa-check-circle"></i> ¡Excelente! No hay productos con stock bajo en este momento.
            </div>
        @endif
    </div>
</div>
@endsection
