@extends('layouts.app')

@section('title', 'Ajuste de Inventario')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-file-import"></i> Ajuste de Inventario</h2>
        <p class="text-muted">Importa el inventario físico y el sistema ajustará automáticamente las existencias</p>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card border-primary">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-warehouse"></i> Importar Inventario Físico</h5>
            </div>
            <div class="card-body">
                <!-- Instrucciones -->
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle"></i> ¿Cómo funciona?</h6>
                    <ol class="mb-0">
                        <li>Selecciona el almacén que vas a inventariar</li>
                        <li>Descarga la plantilla Excel (incluye todos los productos)</li>
                        <li>Llena la columna "existenciaFisica" con el conteo real</li>
                        <li>Sube el archivo y revisa los ajustes que se harán</li>
                        <li>Confirma para aplicar los cambios al inventario</li>
                    </ol>
                </div>

                <!-- Paso 1: Seleccionar Almacén -->
                <div class="card mb-4">
                    <div class="card-header">
                        <strong>Paso 1:</strong> Seleccionar Almacén
                    </div>
                    <div class="card-body">
                        <form action="{{ route('inventarios.import.template') }}" method="GET" id="formTemplate">
                            <div class="row">
                                <div class="col-md-10">
                                    <select name="almacen_id" id="almacen_id" class="form-select" required>
                                        <option value="">Selecciona un almacén...</option>
                                        @foreach($almacenes as $almacen)
                                            <option value="{{ $almacen->id }}">{{ $almacen->nombre }} - {{ $almacen->ciudad }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="fas fa-download"></i> Descargar
                                    </button>
                                </div>
                            </div>
                        </form>
                        <p class="text-muted mt-2 mb-0">
                            <i class="fas fa-lightbulb"></i> La plantilla incluye todos los productos con sus existencias actuales
                        </p>
                    </div>
                </div>

                <!-- Paso 2: Subir Archivo -->
                <div class="card">
                    <div class="card-header">
                        <strong>Paso 2:</strong> Subir Archivo Completado
                    </div>
                    <div class="card-body">
                        <form action="{{ route('inventarios.import.preview') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            
                            <input type="hidden" name="almacen_id" id="almacen_id_upload" value="">
                            
                            <div class="mb-3">
                                <label class="form-label"><i class="fas fa-upload"></i> Archivo Excel</label>
                                <input type="file" class="form-control @error('file') is-invalid @enderror" name="file" accept=".xlsx,.xls" required>
                                @error('file')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Formatos: .xlsx, .xls | Máx: 10 MB</small>
                            </div>

                            <div class="alert alert-warning">
                                <strong><i class="fas fa-exclamation-triangle"></i> Importante:</strong>
                                <ul class="mb-0 mt-2">
                                    <li>El sistema comparará la existencia física con la actual</li>
                                    <li>Se generarán ENTRADAS o SALIDAS según la diferencia</li>
                                    <li>Todos los movimientos se registrarán en el KARDEX</li>
                                    <li>Productos sin cambios no se procesarán</li>
                                </ul>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-eye"></i> Previsualizar Ajustes
                                </button>
                                <a href="{{ route('inventarios.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left"></i> Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Explicación de Ajustes -->
                <div class="card mt-4 border-info">
                    <div class="card-header bg-info text-white">
                        <h6 class="mb-0"><i class="fas fa-question-circle"></i> Tipos de Ajustes</h6>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Situación</th>
                                    <th>Acción del Sistema</th>
                                    <th>Registro en Kardex</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>No hay existencia (0) → Hay física (10)</td>
                                    <td><span class="badge bg-success">ENTRADA +10</span></td>
                                    <td>"Inventario inicial"</td>
                                </tr>
                                <tr>
                                    <td>Existe (20) → Física es mayor (25)</td>
                                    <td><span class="badge bg-primary">ENTRADA +5</span></td>
                                    <td>"Ajuste de inventario"</td>
                                </tr>
                                <tr>
                                    <td>Existe (20) → Física es menor (15)</td>
                                    <td><span class="badge bg-danger">SALIDA -5</span></td>
                                    <td>"Ajuste de inventario"</td>
                                </tr>
                                <tr>
                                    <td>Existe (20) → Física igual (20)</td>
                                    <td><span class="badge bg-secondary">Sin cambio</span></td>
                                    <td>No se registra</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sincronizar almacén seleccionado
document.getElementById('almacen_id').addEventListener('change', function() {
    document.getElementById('almacen_id_upload').value = this.value;
});
</script>
@endpush
