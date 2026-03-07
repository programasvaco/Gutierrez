@extends('layouts.app')
@section('title', 'Previsualización')
@section('content')
<div class="card">
    <div class="card-header bg-info text-white">
        <h5><i class="fas fa-eye"></i> Previsualización de Importación</h5>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-4"><div class="card bg-primary text-white"><div class="card-body text-center"><h3>{{ count($rows) }}</h3><p>Total Filas</p></div></div></div>
            <div class="col-md-4"><div class="card bg-success text-white"><div class="card-body text-center"><h3>{{ collect($rows)->where('status_row', 'ok')->count() }}</h3><p>Sin Errores</p></div></div></div>
            <div class="col-md-4"><div class="card bg-danger text-white"><div class="card-body text-center"><h3>{{ collect($rows)->where('status_row', 'error')->count() }}</h3><p>Con Errores</p></div></div></div>
        </div>
        @if(count($errors) > 0)
        <div class="alert alert-danger"><h6><i class="fas fa-exclamation-circle"></i> Errores</h6><ul class="mb-0">@foreach($errors as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        @if(count($warnings) > 0)
        <div class="alert alert-warning"><h6><i class="fas fa-exclamation-triangle"></i> Advertencias</h6><ul class="mb-0">@foreach($warnings as $warning)<li>{{ $warning }}</li>@endforeach</ul></div>
        @endif
        <div class="table-responsive">
            <table class="table table-sm table-hover">
                <thead class="table-light"><tr><th>Fila</th><th>Nombre</th><th>R. Social</th><th>RFC</th><th>Ciudad</th><th>Teléfono</th><th>correo</th><th>D. Plazo</th></tr></thead>
                <tbody>
                    @foreach($rows as $row)
                    <tr class="{{ $row['status_row'] === 'error' ? 'table-danger' : ($row['status_row'] === 'warning' ? 'table-warning' : '') }}">
                        <td>{{ $row['row_number'] }}</td>
                        <td><strong>{{ $row['nombre'] }}</strong></td>
                        <td>{{ $row['razon_social'] ?: '-' }}</td>
                        <td>{{ $row['rfc'] ?: '-' }}</td>
                        <td>{{ $row['ciudad'] ?: '-' }}</td>
                        <td>{{ $row['telefono'] ?: '-' }}</td>
                        <td>{{ $row['correo'] ?: '-' }}</td>
                        <td>{{ $row['dias_plazo'] ?: '-' }}</td>
                        <td>
                            @if($row['status_row'] === 'ok')<span class="badge bg-success">OK</span>
                            @elseif($row['status_row'] === 'warning')<span class="badge bg-warning text-dark">Actualizar</span>
                            @else<span class="badge bg-danger">Error</span>
                            @endif
                            @if(count($row['errors']) > 0)<br><small class="text-danger">{{ implode(', ', $row['errors']) }}</small>@endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <hr>
        <div class="d-flex justify-content-between">
            <a href="{{ route('proveedores.import') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Volver</a>
            @if(collect($rows)->where('status_row', '!=', 'error')->count() > 0)
            <form action="{{ route('proveedores.import.process') }}" method="POST" style="display:inline;">@csrf
                <button type="submit" class="btn btn-success btn-lg" onclick="return confirm('¿Confirmas la importación?')">
                    <i class="fas fa-check"></i> Confirmar ({{ collect($rows)->where('status_row', '!=', 'error')->count() }})
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection
