@extends('layouts.app')

@section('title', 'Crear Producto')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active">Crear Producto</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Crear Nuevo Producto</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('productos.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <!-- Código -->
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo') }}" required>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Código de Empaque -->
                        <div class="col-md-6 mb-3">
                            <label for="codigo_empaque" class="form-label">Código de Empaque</label>
                            <input type="text" class="form-control @error('codigo_empaque') is-invalid @enderror" id="codigo_empaque" name="codigo_empaque" value="{{ old('codigo_empaque') }}">
                            @error('codigo_empaque')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unidad -->
                        <div class="col-md-6 mb-3">
                            <label for="unidad" class="form-label">Unidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unidad') is-invalid @enderror" id="unidad" name="unidad" value="{{ old('unidad') }}" placeholder="Ej: Pieza, Kg, Litro" required>
                            @error('unidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unidad de Compra -->
                        <div class="col-md-6 mb-3">
                            <label for="unidad_compra" class="form-label">Unidad de Compra <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unidad_compra') is-invalid @enderror" id="unidad_compra" name="unidad_compra" value="{{ old('unidad_compra') }}" placeholder="Ej: Caja, Paquete" required>
                            @error('unidad_compra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contenido -->
                        <div class="col-md-4 mb-3">
                            <label for="contenido" class="form-label">Contenido <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('contenido') is-invalid @enderror" id="contenido" name="contenido" value="{{ old('contenido', 1) }}" required>
                            @error('contenido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock Mínimo -->
                        <div class="col-md-4 mb-3">
                            <label for="stock_min" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock_min') is-invalid @enderror" id="stock_min" name="stock_min" value="{{ old('stock_min', 0) }}" required>
                            @error('stock_min')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock Máximo -->
                        <div class="col-md-4 mb-3">
                            <label for="stock_max" class="form-label">Stock Máximo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock_max') is-invalid @enderror" id="stock_max" name="stock_max" value="{{ old('stock_max', 0) }}" required>
                            @error('stock_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Precio venta -->
                        <div class="col-md-6 mb-3">
                            <label for="precio_venta" class="form-label">Precio venta <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" id="precio_venta" name="precio_venta" value="{{ old('precio_venta', 1) }}" required>
                            @error('precio_venta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Precio mínimo -->
                        <div class="col-md-6 mb-3">
                            <label for="precio_minimo" class="form-label">Precio mínimo <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('precio_minimo') is-invalid @enderror" id="precio_minimo" name="precio_minimo" value="{{ old('precio_minimo', 1) }}" required>
                            @error('precio_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="activo" {{ old('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Imagen -->
                        <div class="col-md-6 mb-3">
                            <label for="imagen" class="form-label">Imagen del Producto</label>
                            <input type="file" class="form-control @error('imagen') is-invalid @enderror" id="imagen" name="imagen" accept="image/*" onchange="previewImage(event)">
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                        </div>

                        <!-- Preview de la imagen -->
                        <div class="col-md-12 mb-3">
                            <div id="imagePreview" style="display: none;">
                                <label class="form-label">Vista previa:</label>
                                <div>
                                    <img id="preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('imagePreview');
            preview.src = reader.result;
            imagePreview.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush
