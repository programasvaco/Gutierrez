@extends('layouts.app')

@section('title', 'Previsualización de Importación')

@section('content')
@if(!isset($rows) || count($rows) == 0)
    <div class="alert alert-warning">
        <i class="fas fa-exclamation-triangle"></i> No hay datos para previsualizar. 
        <a href="{{ route('productos.import') }}" class="alert-link">Volver al formulario de importación</a>
    </div>
@else
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item"><a href="{{ route('productos.import') }}">Importar</a></li>
                <li class="breadcrumb-item active">Previsualización</li>
            </ol>
        </nav>
    </div>
</div>

<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-eye"></i> Previsualización de Importación</h5>
    </div>
    <div class="card-body">
        <!-- Resumen -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h3>{{ count($rows) }}</h3>
                        <p class="mb-0">Total de Filas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h3>{{ collect($rows)->where('status', 'ok')->count() }}</h3>
                        <p class="mb-0">Sin Errores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <h3>{{ collect($rows)->where('status', 'warning')->count() }}</h3>
                        <p class="mb-0">Actualizaciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <h3>{{ collect($rows)->where('status', 'error')->count() }}</h3>
                        <p class="mb-0">Con Errores</p>
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
            <hr>
            <p class="mb-0">Las filas con errores serán <strong>omitidas</strong> durante la importación.</p>
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
            <hr>
            <p class="mb-0">Los productos existentes serán <strong>actualizados</strong> con los nuevos datos.</p>
        </div>
        @endif

        <!-- Tabla de previsualización -->
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Fila</th>
                        <th>Código</th>
                        <th>Descripción</th>
                        <th>Unidad</th>
                        <th class="text-end">Stock Min</th>
                        <th class="text-end">Stock Max</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr class="{{ $row['status'] === 'error' ? 'table-danger' : ($row['status'] === 'warning' ? 'table-warning' : '') }}">
                        <td>{{ $row['row_number'] }}</td>
                        <td><code>{{ $row['codigo'] }}</code></td>
                        <td>{{ Str::limit($row['descripcion'], 50) }}</td>
                        <td>{{ $row['unidad'] }}</td>
                        <td class="text-end">{{ $row['stockMin'] }}</td>
                        <td class="text-end">{{ $row['stockMax'] }}</td>
                        <td>
                            @if($row['status'] === 'ok')
                                <span class="badge bg-success"><i class="fas fa-check"></i> OK</span>
                            @elseif($row['status'] === 'warning')
                                <span class="badge bg-warning text-dark"><i class="fas fa-exclamation-triangle"></i> Actualizar</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times"></i> Error</span>
                                @if(count($row['errors']) > 0)
                                    <br><small class="text-danger">{{ implode(', ', $row['errors']) }}</small>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Acciones -->
        <hr>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <a href="{{ route('productos.import') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Volver
                </a>
            </div>
            <div>
                @if(collect($rows)->where('status', '!=', 'error')->count() > 0)
                    <form action="{{ route('productos.import.process') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('¿Confirmas la importación de {{ collect($rows)->where('status', '!=', 'error')->count() }} productos?')">
                            <i class="fas fa-check"></i> Confirmar Importación
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-secondary" disabled>
                        <i class="fas fa-times"></i> No hay productos válidos para importar
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
@endif
@endsection
