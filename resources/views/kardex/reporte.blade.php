@extends('layouts.app')

@section('title', 'Reporte General de Kardex')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-chart-line"></i> Reporte General de Movimientos</h2>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('kardex.reporte') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="almacen_id" class="form-label">Almacén</label>
                    <select class="form-select" id="almacen_id" name="almacen_id">
                        <option value="">Todos los almacenes</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}">
                </div>

                <div class="col-md-3">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Movimientos Registrados</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('kardex.index') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-clipboard-list"></i> Consulta por Producto
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        @if($movimientos->count() > 0)
            <div class="table-responsive">
                <table class="table table-sm table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha/Hora</th>
                            <th>Producto</th>
                            <th>Almacén</th>
                            <th>Documento</th>
                            <th>Referencia</th>
                            <th>Tipo Mov.</th>
                            <th class="text-end">Cantidad</th>
                            <th class="text-end">Costo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($movimientos as $movimiento)
                        <tr>
                            <td>
                                {{ $movimiento->fecha->format('d/m/Y') }}
                                <br>
                                <small class="text-muted">{{ date('H:i:s', strtotime($movimiento->hora)) }}</small>
                            </td>
                            <td>
                                <strong>{{ $movimiento->producto->codigo }}</strong>
                                <br>
                                <small>{{ $movimiento->producto->descripcion }}</small>
                            </td>
                            <td>
                                <i class="fas fa-warehouse text-primary"></i> {{ $movimiento->almacen->nombre }}
                            </td>
                            <td>{{ $movimiento->documento }}</td>
                            <td><code>{{ $movimiento->referencia_doc }}</code></td>
                            <td>
                                @if($movimiento->tipo_movimiento == 'Entrada')
                                    <span class="badge bg-success"><i class="fas fa-arrow-down"></i> Entrada</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-arrow-up"></i> Salida</span>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format($movimiento->cantidad, 2) }}</td>
                            <td class="text-end">${{ number_format($movimiento->costo, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $movimientos->links() }}
            </div>

            <!-- Estadísticas -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3">
                                    <h6 class="text-muted">Total Movimientos</h6>
                                    <h4>{{ $movimientos->total() }}</h4>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-muted">Entradas</h6>
                                    <h4 class="text-success">{{ $movimientos->where('tipo_movimiento', 'Entrada')->count() }}</h4>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-muted">Salidas</h6>
                                    <h4 class="text-danger">{{ $movimientos->where('tipo_movimiento', 'Salida')->count() }}</h4>
                                </div>
                                <div class="col-md-3">
                                    <h6 class="text-muted">Valor Total</h6>
                                    <h4 class="text-primary">
                                        ${{ number_format($movimientos->sum(function($m) { return $m->cantidad * $m->costo; }), 2) }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron movimientos para los filtros seleccionados.
            </div>
        @endif
    </div>
</div>
@endsection
