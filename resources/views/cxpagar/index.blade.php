@extends('layouts.app')

@section('title', 'Cuentas por Pagar')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-money-bill-wave"></i> Cuentas por Pagar</h2>
    </div>
</div>

<!-- Estadísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-dollar-sign"></i> Total Pendiente</h6>
                <h2 class="mb-0">${{ number_format($totalPendiente, 2) }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-exclamation-circle"></i> Total Vencido</h6>
                <h2 class="mb-0">${{ number_format($totalVencido, 2) }}</h2>
                <small>{{ $cantidadVencidas }} cuentas vencidas</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title"><i class="fas fa-list"></i> Total Cuentas</h6>
                <h2 class="mb-0">{{ $cuentas->total() }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Listado de Cuentas</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('cxpagar.vencidas') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-exclamation-circle"></i> Vencidas ({{ $cantidadVencidas }})
                </a>
                <a href="{{ route('cxpagar.por-proveedor') }}" class="btn btn-info btn-sm">
                    <i class="fas fa-truck"></i> Por Proveedor
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros -->
        <form action="{{ route('cxpagar.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <!-- Búsqueda -->
                <div class="col-md-3">
                    <label class="form-label">Búsqueda</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Referencia..." value="{{ request('search') }}">
                    </div>
                </div>

                <!-- Proveedor -->
                <div class="col-md-3">
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor_id" class="form-select">
                        <option value="">Todos los proveedores</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" {{ request('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                {{ $proveedor->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Estado -->
                <div class="col-md-2">
                    <label class="form-label">Estado</label>
                    <select name="estado" class="form-select">
                        <option value="">Todos</option>
                        <option value="pendientes" {{ request('estado') == 'pendientes' ? 'selected' : '' }}>Pendientes</option>
                        <option value="vencidas" {{ request('estado') == 'vencidas' ? 'selected' : '' }}>Vencidas</option>
                        <option value="pagadas" {{ request('estado') == 'pagadas' ? 'selected' : '' }}>Pagadas</option>
                    </select>
                </div>

                <!-- Fecha Desde -->
                <div class="col-md-2">
                    <label class="form-label">Desde</label>
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>

                <!-- Fecha Hasta -->
                <div class="col-md-2">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}">
                </div>

                <!-- Botones de Filtro -->
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                    <a href="{{ route('cxpagar.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Limpiar
                    </a>
                </div>
            </div>

            <!-- Botones de Ordenamiento -->
            <div class="row mt-3">
                <div class="col-md-12">
                    <label class="form-label"><strong>Ordenar por:</strong></label>
                    <div class="btn-group" role="group">
                        <a href="{{ route('cxpagar.index', array_merge(request()->except(['order_by', 'order_direction']), ['order_by' => 'fecha_vencimiento', 'order_direction' => request('order_by') == 'fecha_vencimiento' && request('order_direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                           class="btn btn-sm {{ request('order_by') == 'fecha_vencimiento' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-calendar-alt"></i> Vencimiento
                            @if(request('order_by') == 'fecha_vencimiento')
                                <i class="fas fa-sort-{{ request('order_direction') == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                        <a href="{{ route('cxpagar.index', array_merge(request()->except(['order_by', 'order_direction']), ['order_by' => 'saldo', 'order_direction' => request('order_by') == 'saldo' && request('order_direction') == 'desc' ? 'asc' : 'desc'])) }}" 
                           class="btn btn-sm {{ request('order_by') == 'saldo' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-dollar-sign"></i> Saldo
                            @if(request('order_by') == 'saldo')
                                <i class="fas fa-sort-{{ request('order_direction') == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                        <a href="{{ route('cxpagar.index', array_merge(request()->except(['order_by', 'order_direction']), ['order_by' => 'proveedor', 'order_direction' => request('order_by') == 'proveedor' && request('order_direction') == 'asc' ? 'desc' : 'asc'])) }}" 
                           class="btn btn-sm {{ request('order_by') == 'proveedor' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-truck"></i> Proveedor
                            @if(request('order_by') == 'proveedor')
                                <i class="fas fa-sort-{{ request('order_direction') == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                        <a href="{{ route('cxpagar.index', array_merge(request()->except(['order_by', 'order_direction']), ['order_by' => 'fecha', 'order_direction' => request('order_by') == 'fecha' && request('order_direction') == 'desc' ? 'asc' : 'desc'])) }}" 
                           class="btn btn-sm {{ request('order_by') == 'fecha' ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="fas fa-calendar"></i> Fecha
                            @if(request('order_by') == 'fecha')
                                <i class="fas fa-sort-{{ request('order_direction') == 'asc' ? 'up' : 'down' }}"></i>
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </form>

        @if($cuentas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Proveedor</th>
                            <th>Compra</th>
                            <th>Vencimiento</th>
                            <th class="text-end">Importe</th>
                            <th class="text-end">Saldo</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Días</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuentas as $cuenta)
                        @php
                            $diasVencimiento = now()->diffInDays($cuenta->fecha_vencimiento, false);
                            $esVencida = $cuenta->vencida;
                        @endphp
                        <tr class="{{ $esVencida && $cuenta->saldo > 0 ? 'table-danger' : '' }}">
                            <td>{{ $cuenta->fecha->format('d/m/Y') }}</td>
                            <td>
                                <strong>{{ $cuenta->proveedor->nombre }}</strong>
                                <br><small class="text-muted">{{ $cuenta->proveedor->rfc }}</small>
                            </td>
                            <td>
                                <a href="{{ route('compras.show', $cuenta->compra) }}" target="_blank" class="text-decoration-none">
                                    <code>{{ $cuenta->compra->referencia }}</code>
                                </a>
                            </td>
                            <td>
                                <strong>{{ $cuenta->fecha_vencimiento->format('d/m/Y') }}</strong>
                                @if($esVencida && $cuenta->saldo > 0)
                                    <br><span class="badge bg-danger"><i class="fas fa-exclamation-triangle"></i> Vencida</span>
                                @endif
                            </td>
                            <td class="text-end">${{ number_format($cuenta->importe, 2) }}</td>
                            <td class="text-end">
                                <strong class="{{ $cuenta->saldo > 0 ? 'text-danger fs-5' : 'text-success' }}">
                                    ${{ number_format($cuenta->saldo, 2) }}
                                </strong>
                            </td>
                            <td class="text-center">
                                @if($cuenta->pagada)
                                    <span class="badge bg-success"><i class="fas fa-check-circle"></i> Pagada</span>
                                @elseif($esVencida && $cuenta->saldo > 0)
                                    <span class="badge bg-danger"><i class="fas fa-exclamation-circle"></i> Vencida</span>
                                @else
                                    <span class="badge bg-warning text-dark"><i class="fas fa-clock"></i> Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if(!$cuenta->pagada)
                                    @if($diasVencimiento < 0)
                                        <span class="badge bg-danger">{{ abs($diasVencimiento) }} días vencida</span>
                                    @elseif($diasVencimiento <= 7)
                                        <span class="badge bg-warning text-dark">{{ $diasVencimiento }} días</span>
                                    @else
                                        <span class="badge bg-info">{{ $diasVencimiento }} días</span>
                                    @endif
                                @else
                                    <span class="badge bg-secondary">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">TOTALES EN PÁGINA:</th>
                            <th class="text-end">${{ number_format($cuentas->sum('importe'), 2) }}</th>
                            <th class="text-end"><strong class="text-danger">${{ number_format($cuentas->sum('saldo'), 2) }}</strong></th>
                            <th colspan="2"></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Mostrando {{ $cuentas->firstItem() }} - {{ $cuentas->lastItem() }} de {{ $cuentas->total() }} cuentas
                </div>
                <div>
                    {{ $cuentas->links() }}
                </div>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron cuentas por pagar con los filtros seleccionados.
            </div>
        @endif
    </div>
</div>
@endsection
