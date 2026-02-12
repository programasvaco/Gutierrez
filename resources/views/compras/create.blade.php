@extends('layouts.app')

@section('title', 'Nueva Compra')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('compras.index') }}">Compras</a></li>
                <li class="breadcrumb-item active">Nueva Compra</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('compras.store') }}" method="POST" id="formCompra">
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
                            <label for="referencia" class="form-label">Referencia <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('referencia') is-invalid @enderror" id="referencia" name="referencia" value="{{ old('referencia') }}" required>
                            @error('referencia')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="proveedor_id" class="form-label">Proveedor <span class="text-danger">*</span></label>
                            <select class="form-select @error('proveedor_id') is-invalid @enderror" id="proveedor_id" name="proveedor_id" required>
                                <option value="">Seleccione...</option>
                                @foreach($proveedores as $proveedor)
                                    <option value="{{ $proveedor->id }}" {{ old('proveedor_id') == $proveedor->id ? 'selected' : '' }}>
                                        {{ $proveedor->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('proveedor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="almacen_id" class="form-label">Almacén <span class="text-danger">*</span></label>
                            <select class="form-select @error('almacen_id') is-invalid @enderror" id="almacen_id" name="almacen_id" required>
                                <option value="">Seleccione...</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}" {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                        {{ $almacen->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('almacen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detalles de la Compra -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Detalles de la Compra</h5>
                    <button type="button" class="btn btn-sm btn-success" onclick="agregarDetalle()">
                        <i class="fas fa-plus"></i> Agregar Producto
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="tablaDetalles">
                            <thead class="table-light">
                                <tr>
                                    <th width="35%">Producto</th>
                                    <th width="15%">Cantidad</th>
                                    <th width="15%">Costo Unitario</th>
                                    <th width="15%">Impuestos</th>
                                    <th width="15%">Subtotal</th>
                                    <th width="5%"></th>
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

        <!-- Totales -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8"></div>
                        <div class="col-md-4">
                            <table class="table table-sm">
                                <tr>
                                    <th>Subtotal:</th>
                                    <td class="text-end"><strong id="subtotalDisplay">$0.00</strong></td>
                                </tr>
                                <tr>
                                    <th>Impuestos:</th>
                                    <td class="text-end"><strong id="impuestosDisplay">$0.00</strong></td>
                                </tr>
                                <tr class="table-primary">
                                    <th>TOTAL:</th>
                                    <td class="text-end"><strong id="totalDisplay">$0.00</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('compras.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Guardar Compra
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
            <select class="form-select form-select-sm" name="detalles[${detalleIndex}][producto_id]" required onchange="actualizarProducto(${detalleIndex})">
                <option value="">Seleccione un producto...</option>
                ${productos.map(p => `<option value="${p.id}">${p.codigo} - ${p.descripcion}</option>`).join('')}
            </select>
        </td>
        <td>
            <input type="number" step="0.01" class="form-control form-control-sm" name="detalles[${detalleIndex}][cantidad]" value="1" min="0.01" required onchange="calcularSubtotal(${detalleIndex})">
        </td>
        <td>
            <input type="number" step="0.01" class="form-control form-control-sm" name="detalles[${detalleIndex}][costo]" value="0" min="0" required onchange="calcularSubtotal(${detalleIndex})">
        </td>
        <td>
            <input type="number" step="0.01" class="form-control form-control-sm" name="detalles[${detalleIndex}][impuestos]" value="0" min="0" required onchange="calcularTotales()">
        </td>
        <td>
            <input type="text" class="form-control form-control-sm text-end" id="subtotal-${detalleIndex}" value="$0.00" readonly>
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
        calcularTotales();
    }
}

function calcularSubtotal(index) {
    const cantidad = parseFloat(document.querySelector(`[name="detalles[${index}][cantidad]"]`).value) || 0;
    const costo = parseFloat(document.querySelector(`[name="detalles[${index}][costo]"]`).value) || 0;
    const subtotal = cantidad * costo;
    
    document.getElementById(`subtotal-${index}`).value = '$' + subtotal.toFixed(2);
    calcularTotales();
}

function calcularTotales() {
    let subtotal = 0;
    let impuestos = 0;
    
    document.querySelectorAll('[id^="detalle-"]').forEach((row, index) => {
        const cantidad = parseFloat(row.querySelector('[name*="[cantidad]"]').value) || 0;
        const costo = parseFloat(row.querySelector('[name*="[costo]"]').value) || 0;
        const impuesto = parseFloat(row.querySelector('[name*="[impuestos]"]').value) || 0;
        
        subtotal += (cantidad * costo);
        impuestos += impuesto;
    });
    
    const total = subtotal + impuestos;
    
    document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('impuestosDisplay').textContent = '$' + impuestos.toFixed(2);
    document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
}

// Agregar primer detalle automáticamente
document.addEventListener('DOMContentLoaded', function() {
    agregarDetalle();
});

// Validar antes de enviar
document.getElementById('formCompra').addEventListener('submit', function(e) {
    const detalles = document.querySelectorAll('[id^="detalle-"]');
    if (detalles.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un producto a la compra.');
        return false;
    }
});
</script>
@endpush
