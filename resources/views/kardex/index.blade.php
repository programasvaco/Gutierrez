@extends('layouts.app')

@section('title', 'Kardex de Inventario')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-clipboard-list"></i> Kardex de Inventario</h2>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Filtros de Búsqueda</h5>
    </div>
    <div class="card-body">
        <form action="{{ route('kardex.index') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="producto_id" class="form-label">Producto <span class="text-danger">*</span></label>
                    <select class="form-select" id="producto_id" name="producto_id" required>
                        <option value="">Seleccione un producto...</option>
                        @foreach($productos as $producto)
                            <option value="{{ $producto->id }}" {{ request('producto_id') == $producto->id ? 'selected' : '' }}>
                                {{ $producto->codigo }} - {{ $producto->descripcion }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label for="almacen_id" class="form-label">Almacén <span class="text-danger">*</span></label>
                    <select class="form-select" id="almacen_id" name="almacen_id" required>
                        <option value="">Seleccione un almacén...</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label for="fecha_desde" class="form-label">Desde</label>
                    <input type="date" class="form-control" id="fecha_desde" name="fecha_desde" value="{{ request('fecha_desde') }}">
                </div>

                <div class="col-md-2">
                    <label for="fecha_hasta" class="form-label">Hasta</label>
                    <input type="date" class="form-control" id="fecha_hasta" name="fecha_hasta" value="{{ request('fecha_hasta') }}">
                </div>

                <div class="col-md-12">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="documento" class="form-label">Tipo de Documento</label>
                            <select class="form-select" id="documento" name="documento">
                                <option value="">Todos los documentos</option>
                                <option value="Compra" {{ request('documento') == 'Compra' ? 'selected' : '' }}>Compra</option>
                                <option value="Cancelación de compra" {{ request('documento') == 'Cancelación de compra' ? 'selected' : '' }}>Cancelación de compra</option>
                                <option value="Salida traspaso" {{ request('documento') == 'Salida traspaso' ? 'selected' : '' }}>Salida traspaso</option>
                                <option value="Recepción traspaso" {{ request('documento') == 'Recepción traspaso' ? 'selected' : '' }}>Recepción traspaso</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label for="tipo_movimiento" class="form-label">Tipo de Movimiento</label>
                            <select class="form-select" id="tipo_movimiento" name="tipo_movimiento">
                                <option value="">Todos los movimientos</option>
                                <option value="Entrada" {{ request('tipo_movimiento') == 'Entrada' ? 'selected' : '' }}>Entrada</option>
                                <option value="Salida" {{ request('tipo_movimiento') == 'Salida' ? 'selected' : '' }}>Salida</option>
                            </select>
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Consultar
                            </button>
                        </div>

                        <div class="col-md-3 d-flex align-items-end">
                            <a href="{{ route('kardex.reporte') }}" class="btn btn-info w-100">
                                <i class="fas fa-chart-line"></i> Reporte General
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

@if($movimientos !== null)
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-box"></i> Kardex: {{ $productoSeleccionado->descripcion }} 
                <span class="ms-3"><i class="fas fa-warehouse"></i> {{ $almacenSeleccionado->nombre }}</span>
            </h5>
        </div>
        <div class="card-body">
            @if($movimientos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Fecha</th>
                                <th>Hora</th>
                                <th>Documento</th>
                                <th>Referencia</th>
                                <th>Tipo Mov.</th>
                                <th class="text-end">Cantidad</th>
                                <th class="text-end">Costo</th>
                                <th class="text-end">Exist. Ant.</th>
                                <th class="text-end">Exist. Nueva</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $movimiento)
                            <tr class="{{ $movimiento->tipo_movimiento == 'Entrada' ? 'table-success' : 'table-danger' }}">
                                <td>{{ $movimiento->fecha->format('d/m/Y') }}</td>
                                <td>{{ date('H:i:s', strtotime($movimiento->hora)) }}</td>
                                <td>{{ $movimiento->documento }}</td>
                                <td><strong>{{ $movimiento->referencia_doc }}</strong></td>
                                <td>
                                    @if($movimiento->tipo_movimiento == 'Entrada')
                                        <span class="badge bg-success"><i class="fas fa-arrow-down"></i> {{ $movimiento->tipo_movimiento }}</span>
                                    @else
                                        <span class="badge bg-danger"><i class="fas fa-arrow-up"></i> {{ $movimiento->tipo_movimiento }}</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ number_format($movimiento->cantidad, 2) }}</td>
                                <td class="text-end">${{ number_format($movimiento->costo, 2) }}</td>
                                <td class="text-end">{{ number_format($movimiento->existencia_anterior, 2) }}</td>
                                <td class="text-end"><strong>{{ number_format($movimiento->existencia_nueva, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <th colspan="5" class="text-end">Totales:</th>
                                <th class="text-end">
                                    <span class="text-success">E: {{ number_format($movimientos->where('tipo_movimiento', 'Entrada')->sum('cantidad'), 2) }}</span>
                                    <br>
                                    <span class="text-danger">S: {{ number_format($movimientos->where('tipo_movimiento', 'Salida')->sum('cantidad'), 2) }}</span>
                                </th>
                                <th colspan="2"></th>
                                <th class="text-end">
                                    <strong>{{ number_format($movimientos->last()->existencia_nueva ?? 0, 2) }}</strong>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Resumen -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-md-3">
                                        <h6 class="text-muted">Total Entradas</h6>
                                        <h4 class="text-success">{{ number_format($movimientos->where('tipo_movimiento', 'Entrada')->sum('cantidad'), 2) }}</h4>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="text-muted">Total Salidas</h6>
                                        <h4 class="text-danger">{{ number_format($movimientos->where('tipo_movimiento', 'Salida')->sum('cantidad'), 2) }}</h4>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="text-muted">Existencia Final</h6>
                                        <h4 class="text-primary">{{ number_format($movimientos->last()->existencia_nueva ?? 0, 2) }}</h4>
                                    </div>
                                    <div class="col-md-3">
                                        <h6 class="text-muted">Movimientos</h6>
                                        <h4>{{ $movimientos->count() }}</h4>
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
@else
    <div class="alert alert-info text-center">
        <i class="fas fa-info-circle"></i> Seleccione un producto y almacén para consultar el kardex.
    </div>
@endif
@endsection
