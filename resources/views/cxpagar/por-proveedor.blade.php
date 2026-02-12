@extends('layouts.app')

@section('title', 'Cuentas por Proveedor')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-truck"></i> Cuentas por Pagar - Por Proveedor</h2>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Seleccionar Proveedor</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('cxpagar.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('cxpagar.por-proveedor') }}" method="GET">
            <div class="row g-3">
                <div class="col-md-10">
                    <label class="form-label">Proveedor</label>
                    <select name="proveedor_id" class="form-select" required>
                        <option value="">Seleccione un proveedor...</option>
                        @foreach($proveedores as $prov)
                            <option value="{{ $prov->id }}" {{ request('proveedor_id') == $prov->id ? 'selected' : '' }}>
                                {{ $prov->nombre }} - {{ $prov->razon_social }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Consultar
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(isset($proveedor) && isset($cuentas))
<div class="card mt-4">
    <div class="card-header bg-light">
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-0">
                    <i class="fas fa-truck text-primary"></i> {{ $proveedor->nombre }}
                </h5>
                <small class="text-muted">
                    {{ $proveedor->razon_social }} - RFC: {{ $proveedor->rfc }}
                </small>
                @if($proveedor->telefono)
                    <br><small><i class="fas fa-phone"></i> {{ $proveedor->telefono }}</small>
                @endif
                @if($proveedor->correo)
                    <br><small><i class="fas fa-envelope"></i> {{ $proveedor->correo }}</small>
                @endif
            </div>
            <div class="col-md-6 text-end">
                @if($proveedor->diasPlazo)
                    <span class="badge bg-info fs-6">Plazo: {{ $proveedor->diasPlazo }} días</span>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Resumen -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h6>Total Importe</h6>
                        <h2>${{ number_format($totalImporte, 2) }}</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h6>Saldo Pendiente</h6>
                        <h2>${{ number_format($totalSaldo, 2) }}</h2>
                    </div>
                </div>
            </div>
        </div>

        @if($cuentas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
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
                        @endphp
                        <tr class="{{ $cuenta->vencida && $cuenta->saldo > 0 ? 'table-danger' : '' }}">
                            <td>{{ $cuenta->fecha->format('d/m/Y') }}</td>
                            <td>
                                <a href="{{ route('compras.show', $cuenta->compra) }}" target="_blank" class="text-decoration-none">
                                    <code>{{ $cuenta->compra->referencia }}</code>
                                </a>
                            </td>
                            <td>
                                <strong>{{ $cuenta->fecha_vencimiento->format('d/m/Y') }}</strong>
                            </td>
                            <td class="text-end">${{ number_format($cuenta->importe, 2) }}</td>
                            <td class="text-end">
                                <strong class="{{ $cuenta->saldo > 0 ? 'text-danger' : 'text-success' }}">
                                    ${{ number_format($cuenta->saldo, 2) }}
                                </strong>
                            </td>
                            <td class="text-center">
                                @if($cuenta->pagada)
                                    <span class="badge bg-success"><i class="fas fa-check"></i> Pagada</span>
                                @elseif($cuenta->vencida && $cuenta->saldo > 0)
                                    <span class="badge bg-danger"><i class="fas fa-exclamation"></i> Vencida</span>
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
                            <th colspan="3" class="text-end">TOTALES:</th>
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
                <i class="fas fa-info-circle"></i> Este proveedor no tiene cuentas por pagar registradas.
            </div>
        @endif
    </div>
</div>
@endif
@endsection
