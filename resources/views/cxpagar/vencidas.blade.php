@extends('layouts.app')

@section('title', 'Cuentas Vencidas')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-exclamation-circle text-danger"></i> Cuentas por Pagar Vencidas</h2>
    </div>
</div>

<div class="card border-danger">
    <div class="card-header bg-danger text-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">
                    <i class="fas fa-exclamation-triangle"></i> 
                    Cuentas Vencidas - Total: ${{ number_format($totalVencido, 2) }}
                </h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('cxpagar.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtro -->
        <form action="{{ route('cxpagar.vencidas') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-10">
                    <label class="form-label">Filtrar por Proveedor</label>
                    <select name="proveedor_id" class="form-select">
                        <option value="">Todos los proveedores</option>
                        @foreach($proveedores as $proveedor)
                            <option value="{{ $proveedor->id }}" {{ request('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                {{ $proveedor->nombre }}
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

        @if($cuentas->count() > 0)
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle"></i> 
                <strong>{{ $cuentas->total() }}</strong> cuentas vencidas por un total de 
                <strong>${{ number_format($totalVencido, 2) }}</strong>
            </div>

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Proveedor</th>
                            <th>Compra</th>
                            <th>Fecha</th>
                            <th>Vencimiento</th>
                            <th class="text-end">Saldo</th>
                            <th class="text-center">Días Vencida</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cuentas as $cuenta)
                        @php
                            $diasVencida = abs(now()->diffInDays($cuenta->fecha_vencimiento, false));
                        @endphp
                        <tr class="table-danger">
                            <td>
                                <strong>{{ $cuenta->proveedor->nombre }}</strong>
                                <br><small class="text-muted">{{ $cuenta->proveedor->rfc }}</small>
                                @if($cuenta->proveedor->telefono)
                                    <br><small><i class="fas fa-phone"></i> {{ $cuenta->proveedor->telefono }}</small>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('compras.show', $cuenta->compra) }}" target="_blank" class="text-decoration-none">
                                    <code>{{ $cuenta->compra->referencia }}</code>
                                </a>
                            </td>
                            <td>{{ $cuenta->fecha->format('d/m/Y') }}</td>
                            <td>
                                <strong class="text-danger">{{ $cuenta->fecha_vencimiento->format('d/m/Y') }}</strong>
                            </td>
                            <td class="text-end">
                                <strong class="text-danger fs-4">${{ number_format($cuenta->saldo, 2) }}</strong>
                            </td>
                            <td class="text-center">
                                @if($diasVencida >= 30)
                                    <span class="badge bg-danger fs-6">{{ $diasVencida }} días</span>
                                @elseif($diasVencida >= 15)
                                    <span class="badge bg-warning text-dark fs-6">{{ $diasVencida }} días</span>
                                @else
                                    <span class="badge bg-info fs-6">{{ $diasVencida }} días</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="4" class="text-end">TOTAL VENCIDO:</th>
                            <th class="text-end">
                                <strong class="text-danger fs-4">${{ number_format($cuentas->sum('saldo'), 2) }}</strong>
                            </th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Mostrando {{ $cuentas->firstItem() }} - {{ $cuentas->lastItem() }} de {{ $cuentas->total() }} cuentas vencidas
                </div>
                <div>
                    {{ $cuentas->links() }}
                </div>
            </div>
        @else
            <div class="alert alert-success text-center">
                <i class="fas fa-check-circle"></i> ¡Excelente! No hay cuentas vencidas en este momento.
            </div>
        @endif
    </div>
</div>
@endsection
