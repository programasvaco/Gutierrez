@extends('layouts.app')

@section('title', 'Nuevo Traspaso')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('traspasos.index') }}">Traspasos</a></li>
                <li class="breadcrumb-item active">Nuevo Traspaso</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('traspasos.store') }}" method="POST" id="formTraspaso">
    @csrf
    
    <div class="row">
        <!-- Información General -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Información General</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('fecha') is-invalid @enderror" id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="folio" class="form-label">Folio <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('folio') is-invalid @enderror" id="folio" name="folio" value="{{ old('folio') }}" required>
                            @error('folio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="almacen_origen_id" class="form-label">Almacén Origen <span class="text-danger">*</span></label>
                            <select class="form-select @error('almacen_origen_id') is-invalid @enderror" id="almacen_origen_id" name="almacen_origen_id" required>
                                <option value="">Seleccione...</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}" {{ old('almacen_origen_id') == $almacen->id ? 'selected' : '' }}>
                                        {{ $almacen->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('almacen_origen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="almacen_destino_id" class="form-label">Almacén Destino <span class="text-danger">*</span></label>
                            <select class="form-select @error('almacen_destino_id') is-invalid @enderror" id="almacen_destino_id" name="almacen_destino_id" required>
                                <option value="">Seleccione...</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}" {{ old('almacen_destino_id') == $almacen->id ? 'selected' : '' }}>
                                        {{ $almacen->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('almacen_destino_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="observaciones" class="form-label">Observaciones</label>
                            <textarea class="form-control @error('observaciones') is-invalid @enderror" id="observaciones" name="observaciones" rows="2" maxlength="500">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Máximo 500 caracteres</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles del Traspaso -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Productos a Traspasar</h5>
                    <button type="button" class="btn btn-sm btn-success" onclick="agregarDetalle()">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaDetalles">
                            <thead class="table-light">
                                <tr>
                                    <th width="50%">Producto</th>
                                    <th width="20%">Cantidad</th>
                                    <th width="20%">Costo Unitario</th>
                                    <th width="10%"></th>
                                </tr>
                            </thead>
                            <tbody id="detallesBody">
                                <!-- Los detalles se agregarán dinámicamente aquí -->
                            </tbody>
                        </table>
                    </div>

                    @error('detalles')
                        <div class="alert alert-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('traspasos.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Traspaso
        </button>
    </div>
</form>
@endsection

@push('scripts')
<script>
let detalleIndex = 0;
const productos = @json($productos);

function agregarDetalle() {
    const tbody = document.getElementById('detallesBody');
    const row = document.createElement('tr');
    row.id = `detalle-${detalleIndex}`;
    
    row.innerHTML = `
        <td>
            <select class="form-select form-select-sm" name="detalles[${detalleIndex}][producto_id]" required>
                <option value="">Seleccione un producto...</option>
                ${productos.map(p => `<option value="${p.id}">${p.codigo} - ${p.descripcion}</option>`).join('')}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" class="form-control form-control-sm" name="detalles[${detalleIndex}][cantidad]" value="1" min="0.01" required>
        </td>
        <td>
            <input type="number" step="0.01" class="form-control form-control-sm" name="detalles[${detalleIndex}][costo]" value="0" min="0" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDetalle(${detalleIndex})">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;
    
    tbody.appendChild(row);
    detalleIndex++;
}

function eliminarDetalle(index) {
    const row = document.getElementById(`detalle-${index}`);
    if (row) {
        row.remove();
    }
}

// Agregar primer detalle automáticamente
document.addEventListener('DOMContentLoaded', function() {
    agregarDetalle();
    
    // Validar que los almacenes sean diferentes
    const almacenOrigen = document.getElementById('almacen_origen_id');
    const almacenDestino = document.getElementById('almacen_destino_id');
    
    function validarAlmacenes() {
        if (almacenOrigen.value && almacenDestino.value && almacenOrigen.value === almacenDestino.value) {
            almacenDestino.setCustomValidity('El almacén destino debe ser diferente al origen');
        } else {
            almacenDestino.setCustomValidity('');
        }
    }
    
    almacenOrigen.addEventListener('change', validarAlmacenes);
    almacenDestino.addEventListener('change', validarAlmacenes);
});

// Validar antes de enviar
document.getElementById('formTraspaso').addEventListener('submit', function(e) {
    const detalles = document.querySelectorAll('[id^="detalle-"]');
    if (detalles.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto al traspaso.');
        return false;
    }
    
    const almacenOrigen = document.getElementById('almacen_origen_id').value;
    const almacenDestino = document.getElementById('almacen_destino_id').value;
    
    if (almacenOrigen === almacenDestino) {
        e.preventDefault();
        alert('El almacén origen y destino deben ser diferentes.');
        return false;
    }
});
</script>
@endpush
