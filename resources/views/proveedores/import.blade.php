@extends('layouts.app')

@section('title', 'Importar Proveedores')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('proveedores.index') }}">Proveedores</a></li>
                <li class="breadcrumb-item active">Importar</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-file-import"></i> Importar Proveedores desde Excel</h5>
            </div>
            <div class="card-body">
                <!-- Instrucciones -->
                <div class="alert alert-info">
                    <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Instrucciones</h6>
                    <ol class="mb-0">
                        <li>Descarga la plantilla de Excel</li>
                        <li>Llena los datos de tus proveedores</li>
                        <li>Solo el <strong>nombre</strong> y <strong>status</strong> son obligatorios</li>
                        <li>Guarda el archivo y súbelo aquí</li>
                        <li>Revisa la previsualización antes de confirmar</li>
                    </ol>
                </div>

                <!-- Botón descargar plantilla -->
                <div class="text-center mb-4">
                    <a href="{{ route('proveedores.import.template') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-download"></i> Descargar Plantilla de Excel
                    </a>
                    <p class="text-muted mt-2">
                        <small>La plantilla incluye ejemplos e instrucciones detalladas</small>
                    </p>
                </div>

                <hr>

                <!-- Formulario de carga -->
                <form action="{{ route('proveedores.import.preview') }}" method="POST" enctype="multipart/form-data" id="importForm">
                    @csrf
                    
                    <div class="mb-4">
                        <label for="file" class="form-label">
                            <i class="fas fa-upload"></i> Selecciona el archivo Excel
                        </label>
                        <input type="file" class="form-control @error('file') is-invalid @enderror" id="file" name="file" accept=".xlsx,.xls" required>
                        @error('file')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            Formatos permitidos: .xlsx, .xls | Tamaño máximo: 10 MB
                        </small>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Importante:</strong>
                        <ul class="mb-0 mt-2">
                            <li>Proveedores con nombres existentes serán <strong>actualizados</strong></li>
                            <li>Proveedores nuevos serán <strong>creados</strong></li>
                            <li>No se eliminarán proveedores existentes</li>
                            <li>Solo <strong>nombre</strong> y <strong>status</strong> son obligatorios</li>
                        </ul>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-success" id="btnSubmit">
                            <i class="fas fa-eye"></i> Previsualizar Importación
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Tarjeta de ayuda -->
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-question-circle"></i> Campos de la Plantilla</h6>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Campo</th>
                            <th>Descripción</th>
                            <th>Obligatorio</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><code>nombre</code></td>
                            <td>Nombre del proveedor</td>
                            <td><span class="badge bg-danger">Sí</span></td>
                        </tr>
                        <tr>
                            <td><code>razon_social</code></td>
                            <td>Razón social de la empresa</td>
                            <td><span class="badge bg-secondary">No</span></td>
                        </tr>
                        <tr>
                            <td><code>rfc</code></td>
                            <td>RFC del proveedor</td>
                            <td><span class="badge bg-secondary">No</span></td>
                        </tr>
                        <tr>
                            <td><code>direccion</code></td>
                            <td>Domicilio completo</td>
                            <td><span class="badge bg-secondary">No</span></td>
                        </tr>
                        <tr>
                            <td><code>ciudad</code></td>
                            <td>Ciudad</td>
                            <td><span class="badge bg-secondary">No</span></td>
                        </tr>
                        <tr>
                            <td><code>telefono</code></td>
                            <td>Teléfono de contacto</td>
                            <td><span class="badge bg-secondary">No</span></td>
                        </tr>
                        <tr>
                            <td><code>correo</code></td>
                            <td>Correo electrónico</td>
                            <td><span class="badge bg-secondary">No</span></td>
                        </tr>
                        <tr>
                            <td><code>dias_plazo</code></td>
                            <td>Dias para el credito</td>
                            <td><span class="badge bg-secondary">No</span></td>
                        </tr>
                        <tr>
                            <td><code>status</code></td>
                            <td>Estado: "activo" o "inactivo"</td>
                            <td><span class="badge bg-danger">Sí</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('importForm').addEventListener('submit', function(e) {
    const btn = document.getElementById('btnSubmit');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Procesando...';
});

document.getElementById('file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const fileSize = file.size / 1024 / 1024;
        if (fileSize > 10) {
            alert('El archivo es demasiado grande. El tamaño máximo es 10 MB.');
            this.value = '';
        }
    }
});
</script>
@endpush
