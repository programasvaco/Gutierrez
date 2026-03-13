@extends('layouts.app')

@section('title', 'Nueva Venta')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Ventas</a></li>
                <li class="breadcrumb-item active">Nueva Venta</li>
            </ol>
        </nav>
    </div>
</div>

<form action="{{ route('ventas.store') }}" method="POST" id="formVenta">
    @csrf

    <div class="row">
        <!-- Encabezado -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Datos de la Venta</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label for="fecha" class="form-label">Fecha <span class="text-danger">*</span></label>
                            <input type="date" class="form-control @error('fecha') is-invalid @enderror"
                                id="fecha" name="fecha" value="{{ old('fecha', date('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="almacen_id" class="form-label">Almacén <span class="text-danger">*</span></label>
                            <select class="form-select @error('almacen_id') is-invalid @enderror"
                                id="almacen_id" name="almacen_id" required>
                                <option value="">Seleccione almacén...</option>
                                @foreach($almacenes as $almacen)
                                    <option value="{{ $almacen->id }}" {{ old('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                        {{ $almacen->nombre }} — {{ $almacen->ciudad }}
                                    </option>
                                @endforeach
                            </select>
                            @error('almacen_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="tipo_pago" class="form-label">Tipo de Pago <span class="text-danger">*</span></label>
                            <select class="form-select @error('tipo_pago') is-invalid @enderror"
                                id="tipo_pago" name="tipo_pago" required>
                                <option value="contado" {{ old('tipo_pago', 'contado') === 'contado' ? 'selected' : '' }}>Contado</option>
                                <option value="credito" {{ old('tipo_pago') === 'credito' ? 'selected' : '' }}>Crédito</option>
                            </select>
                            @error('tipo_pago')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cliente_id" class="form-label">Cliente <small class="text-muted">(opcional — mostrador si se deja vacío)</small></label>
                            <select class="form-select @error('cliente_id') is-invalid @enderror"
                                id="cliente_id" name="cliente_id">
                                <option value="">— Venta de mostrador —</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}" {{ old('cliente_id') == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Productos -->
        <div class="col-md-12 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Artículos</h5>
                    <button type="button" class="btn btn-sm btn-success" id="btnAgregarProducto" onclick="agregarDetalle()" disabled>
                        <i class="fas fa-plus"></i> Agregar Artículo
                    </button>
                </div>
                <div class="card-body">
                    <div id="sinAlmacen" class="alert alert-warning text-center">
                        <i class="fas fa-arrow-up"></i> Seleccione un almacén para ver los artículos disponibles.
                    </div>

                    <div id="contenedorDetalles" style="display:none;">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="tablaDetalles">
                                <thead class="table-light">
                                    <tr>
                                        <th width="38%">Artículo</th>
                                        <th width="13%" class="text-center">Existencia</th>
                                        <th width="13%">Cantidad</th>
                                        <th width="15%">Precio Unit.</th>
                                        <th width="14%" class="text-end">Subtotal</th>
                                        <th width="7%"></th>
                                    </tr>
                                </thead>
                                <tbody id="detallesBody"></tbody>
                            </table>
                        </div>
                        @error('detalles')
                            <div class="alert alert-danger mt-2">{{ $message }}</div>
                        @enderror
                    </div>
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
                            <table class="table table-sm mb-0">
                                <tr class="table-primary">
                                    <th class="fs-5">TOTAL:</th>
                                    <td class="text-end fs-5"><strong id="totalDisplay">$0.00</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('ventas.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary btn-lg" id="btnGuardar" disabled>
            <i class="fas fa-save"></i> Registrar Venta
        </button>
    </div>
</form>
@endsection

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
.select2-container { width: 100% !important; }
.select2-container .select2-selection--single { height: 31px; border: 1px solid #dee2e6; border-radius: 4px; }
.select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 29px; font-size: 0.875rem; }
.select2-container--default .select2-selection--single .select2-selection__arrow { height: 29px; }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
let detalleIndex = 0;
let almacenSeleccionado = null;
const productosBaseUrl = "{{ url('ventas/productos') }}";

document.getElementById('almacen_id').addEventListener('change', function () {
    const almacenId = this.value;
    const btnAgregar = document.getElementById('btnAgregarProducto');
    const btnGuardar = document.getElementById('btnGuardar');
    const contenedor = document.getElementById('contenedorDetalles');
    const sinAlmacen = document.getElementById('sinAlmacen');

    // Limpiar filas existentes
    document.getElementById('detallesBody').innerHTML = '';
    detalleIndex = 0;
    calcularTotal();

    if (!almacenId) {
        almacenSeleccionado = null;
        btnAgregar.disabled = true;
        btnGuardar.disabled = true;
        contenedor.style.display = 'none';
        sinAlmacen.style.display = 'block';
        sinAlmacen.innerHTML = '<i class="fas fa-arrow-up"></i> Seleccione un almacén para ver los artículos disponibles.';
        return;
    }

    // Verificar que el almacén tenga existencias (petición rápida sin ?q)
    fetch(`${productosBaseUrl}/${almacenId}?limit=1`)
        .then(r => r.json())
        .then(data => {
            if (data.length === 0) {
                almacenSeleccionado = null;
                sinAlmacen.innerHTML = '<i class="fas fa-exclamation-circle text-warning"></i> Este almacén no tiene existencias disponibles.';
                sinAlmacen.style.display = 'block';
                contenedor.style.display = 'none';
                btnAgregar.disabled = true;
                btnGuardar.disabled = true;
            } else {
                almacenSeleccionado = almacenId;
                sinAlmacen.style.display = 'none';
                contenedor.style.display = 'block';
                btnAgregar.disabled = false;
                btnGuardar.disabled = false;
                agregarDetalle();
            }
        })
        .catch(() => {
            sinAlmacen.innerHTML = '<i class="fas fa-times-circle text-danger"></i> Error al cargar los artículos.';
            sinAlmacen.style.display = 'block';
        });
});

function initSelect2Venta(idx) {
    $(`#producto-select-${idx}`).select2({
        placeholder: 'Buscar artículo...',
        minimumInputLength: 1,
        ajax: {
            url: `${productosBaseUrl}/${almacenSeleccionado}`,
            dataType: 'json',
            delay: 300,
            data: params => ({ q: params.term }),
            processResults: data => ({ results: data.results }),
            cache: true,
        },
    }).on('select2:select', function (e) {
        const data = e.params.data;
        document.querySelector(`[name="detalles[${idx}][producto_id]"]`).value = data.id;

        const precio     = data.precio_venta || 0;
        const existencia = data.existencia   || 0;

        document.getElementById(`precio-${idx}`).value      = precio.toFixed(2);
        document.getElementById(`exist-${idx}`).textContent  = existencia.toFixed(2);
        document.getElementById(`exist-${idx}`).className    = existencia > 0 ? 'badge bg-success' : 'badge bg-danger';
        document.getElementById(`cant-${idx}`).max           = existencia;

        calcularSubtotal(idx);
    });
}

function agregarDetalle() {
    if (!almacenSeleccionado) return;

    const tbody = document.getElementById('detallesBody');
    const idx   = detalleIndex;
    const row   = document.createElement('tr');
    row.id = `detalle-${idx}`;

    row.innerHTML = `
        <td>
            <input type="hidden" name="detalles[${idx}][producto_id]" required>
            <select id="producto-select-${idx}" style="width:100%"></select>
        </td>
        <td class="text-center">
            <span id="exist-${idx}" class="badge bg-secondary">—</span>
        </td>
        <td>
            <input type="number" step="0.01" min="0.01"
                class="form-control form-control-sm"
                name="detalles[${idx}][cantidad]"
                id="cant-${idx}" value="1" required
                onchange="onCantidadChange(${idx})">
        </td>
        <td>
            <input type="number" step="0.01" min="0"
                class="form-control form-control-sm"
                name="detalles[${idx}][precio]"
                id="precio-${idx}" value="0" required
                onchange="calcularSubtotal(${idx})">
        </td>
        <td class="text-end align-middle">
            <strong id="sub-${idx}">$0.00</strong>
        </td>
        <td class="text-center align-middle">
            <button type="button" class="btn btn-sm btn-danger" onclick="eliminarDetalle(${idx})">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    `;

    tbody.appendChild(row);
    initSelect2Venta(idx);
    detalleIndex++;
}

function onCantidadChange(idx) {
    const cantInput  = document.getElementById(`cant-${idx}`);
    const existBadge = document.getElementById(`exist-${idx}`);
    const existencia = parseFloat(existBadge.textContent) || 0;
    const cantidad   = parseFloat(cantInput.value) || 0;

    if (existencia > 0 && cantidad > existencia) {
        cantInput.value = existencia;
        cantInput.classList.add('is-invalid');
        alert(`No hay suficiente existencia. Máximo disponible: ${existencia}`);
    } else {
        cantInput.classList.remove('is-invalid');
    }

    calcularSubtotal(idx);
}

function calcularSubtotal(idx) {
    const cantidad = parseFloat(document.getElementById(`cant-${idx}`).value) || 0;
    const precio   = parseFloat(document.getElementById(`precio-${idx}`).value) || 0;
    document.getElementById(`sub-${idx}`).textContent = '$' + (cantidad * precio).toFixed(2);
    calcularTotal();
}

function calcularTotal() {
    let total = 0;
    document.querySelectorAll('[id^="sub-"]').forEach(el => {
        total += parseFloat(el.textContent.replace('$', '')) || 0;
    });
    document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
}

function eliminarDetalle(idx) {
    const row = document.getElementById(`detalle-${idx}`);
    if (row) { row.remove(); calcularTotal(); }
}

document.getElementById('formVenta').addEventListener('submit', function (e) {
    const filas = document.querySelectorAll('[id^="detalle-"]');
    if (filas.length === 0) {
        e.preventDefault();
        alert('Debe agregar al menos un artículo.');
        return false;
    }

    let valido = true;
    filas.forEach((row) => {
        const hiddenId = row.querySelector('input[type="hidden"]');
        const cant     = row.querySelector('[name*="[cantidad]"]');
        if (!hiddenId.value || parseFloat(cant.value) <= 0) valido = false;
    });

    if (!valido) {
        e.preventDefault();
        alert('Complete todos los campos y asegúrese de que las cantidades sean mayores a cero.');
    }
});
</script>
@endpush
