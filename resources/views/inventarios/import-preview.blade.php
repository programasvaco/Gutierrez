@extends('layouts.app')

@section('title', 'Previsualización de Ajustes')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('inventarios.index') }}">Inventarios</a></li>
                <li class="breadcrumb-item"><a href="{{ route('inventarios.import') }}">Importar</a></li>
                <li class="breadcrumb-item active">Previsualización</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="fas fa-eye"></i> Previsualización de Ajustes - {{ $almacen->nombre }}
        </h5>
    </div>
    <div class="card-body">
        <!-- Resumen de Ajustes -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ $contadores['inventario_inicial'] }}</h3>
                        <p class="mb-0">Inventario Inicial</p>
                        <small>(Sin existencia → Con existencia)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $contadores['entradas'] }}</h3>
                        <p class="mb-0">Ajustes Positivos</p>
                        <small>(Entradas)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ $contadores['salidas'] }}</h3>
                        <p class="mb-0">Ajustes Negativos</p>
                        <small>(Salidas)</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-secondary text-white">
                    <div class="card-body text-center">
                        <h3>{{ $contadores['sin_cambio'] }}</h3>
                        <p class="mb-0">Sin Cambios</p>
                        <small>(No se procesarán)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Errores -->
        @if(count($errors) > 0)
        <div class="alert alert-danger">
            <h6 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Errores Encontrados</h6>
            <ul class="mb-0">
                @foreach($errors as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Advertencias -->
        @if(count($warnings) > 0)
        <div class="alert alert-warning">
            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Advertencias</h6>
            <ul class="mb-0">
                @foreach($warnings as $warning)
                    <li>{{ $warning }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- Información Importante -->
        <div class="alert alert-info">
            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Lo que sucederá al confirmar:</h6>
            <ul class="mb-0">
                <li>Se actualizarán las existencias en el almacén <strong>{{ $almacen->nombre }}</strong></li>
                <li>Cada ajuste se registrará como un movimiento en el <strong>KARDEX</strong></li>
                <li>Los productos sin cambios <strong>NO</strong> se procesarán</li>
                <li>Este proceso <strong>NO</strong> se puede deshacer fácilmente</li>
            </ul>
        </div>

        <!-- Tabla de Ajustes -->
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Fila</th>
                        <th>Código</th>
                        <th>Producto</th>
                        <th class="text-end">Exist. Actual</th>
                        <th class="text-end">Exist. Física</th>
                        <th class="text-end">Diferencia</th>
                        <th class="text-end">Costo</th>
                        <th class="text-center">Tipo de Ajuste</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr class="
                        @if($row['status'] === 'inventario_inicial') table-success
                        @elseif($row['status'] === 'entrada') table-primary
                        @elseif($row['status'] === 'salida') table-danger
                        @elseif($row['status'] === 'warning') table-warning
                        @endif
                    ">
                        <td>{{ $row['row_number'] }}</td>
                        <td><code>{{ $row['codigo'] }}</code></td>
                        <td>{{ Str::limit($row['descripcion'], 40) }}</td>
                        <td class="text-end">{{ number_format($row['existencia_actual'], 2) }}</td>
                        <td class="text-end"><strong>{{ number_format($row['existencia_fisica'], 2) }}</strong></td>
                        <td class="text-end">
                            @if($row['diferencia'] > 0)
                                <span class="text-success"><strong>+{{ number_format($row['diferencia'], 2) }}</strong></span>
                            @elseif($row['diferencia'] < 0)
                                <span class="text-danger"><strong>{{ number_format($row['diferencia'], 2) }}</strong></span>
                            @else
                                <span class="text-secondary">0.00</span>
                            @endif
                        </td>
                        <td class="text-end">${{ number_format($row['costo'], 2) }}</td>
                        <td class="text-center">
                            @if($row['status'] === 'inventario_inicial')
                                <span class="badge bg-success">
                                    <i class="fas fa-plus-circle"></i> {{ $row['tipo_movimiento'] }}
                                </span>
                            @elseif($row['status'] === 'entrada')
                                <span class="badge bg-primary">
                                    <i class="fas fa-arrow-up"></i> {{ $row['tipo_movimiento'] }}
                                </span>
                            @elseif($row['status'] === 'salida')
                                <span class="badge bg-danger">
                                    <i class="fas fa-arrow-down"></i> {{ $row['tipo_movimiento'] }}
                                </span>
                            @elseif($row['status'] === 'sin_cambio')
                                <span class="badge bg-secondary">
                                    <i class="fas fa-equals"></i> {{ $row['tipo_movimiento'] }}
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="fas fa-exclamation"></i> Error
                                </span>
                            @endif
                            @if(count($row['errors']) > 0)
                                <br><small class="text-danger">{{ implode(', ', $row['errors']) }}</small>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totales -->
        <div class="card bg-light mt-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Resumen de Movimientos</h6>
                        <p class="mb-1">
                            <span class="badge bg-success">{{ $contadores['inventario_inicial'] }}</span> Inventario inicial
                        </p>
                        <p class="mb-1">
                            <span class="badge bg-primary">{{ $contadores['entradas'] }}</span> Ajustes positivos
                        </p>
                        <p class="mb-1">
                            <span class="badge bg-danger">{{ $contadores['salidas'] }}</span> Ajustes negativos
                        </p>
                        <p class="mb-0">
                            <span class="badge bg-secondary">{{ $contadores['sin_cambio'] }}</span> Sin cambios
                        </p>
                    </div>
                    <div class="col-md-6 text-end">
                        <h6>Total a Procesar</h6>
                        <h2 class="text-primary">
                            {{ $contadores['inventario_inicial'] + $contadores['entradas'] + $contadores['salidas'] }}
                        </h2>
                        <p class="text-muted mb-0">movimientos de inventario</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones -->
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('inventarios.import') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver a Subir Archivo
                </a>
            </div>
            <div>
                @php
                    $totalProcesar = $contadores['inventario_inicial'] + $contadores['entradas'] + $contadores['salidas'];
                @endphp
                @if($totalProcesar > 0)
                    <form action="{{ route('inventarios.import.process') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg" 
                                onclick="return confirm('¿Confirmas aplicar {{ $totalProcesar }} ajustes de inventario?\n\nEsto actualizará las existencias y registrará movimientos en el Kardex.')">
                            <i class="fas fa-check-circle"></i> Confirmar y Aplicar Ajustes ({{ $totalProcesar }})
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="fas fa-times"></i> No hay ajustes para procesar
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .table-success { background-color: #d1f5d3 !important; }
    .table-primary { background-color: #cfe2ff !important; }
    .table-danger { background-color: #f8d7da !important; }
    .table-warning { background-color: #fff3cd !important; }
</style>
@endpush