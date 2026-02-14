@extends('layouts.app')

@section('title', 'Editar Producto')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active">Editar Producto</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Editar Producto: {{ $producto->codigo }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('productos.update', $producto) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Código -->
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo', $producto->codigo) }}" required>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Código de Empaque -->
                        <div class="col-md-6 mb-3">
                            <label for="codigo_empaque" class="form-label">Código de Empaque</label>
                            <input type="text" class="form-control @error('codigo_empaque') is-invalid @enderror" id="codigo_empaque" name="codigo_empaque" value="{{ old('codigo_empaque', $producto->codigo_empaque) }}">
                            @error('codigo_empaque')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="col-md-12 mb-3">
                            <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" rows="3" required>{{ old('descripcion', $producto->descripcion) }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unidad -->
                        <div class="col-md-6 mb-3">
                            <label for="unidad" class="form-label">Unidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unidad') is-invalid @enderror" id="unidad" name="unidad" value="{{ old('unidad', $producto->unidad) }}" placeholder="Ej: Pieza, Kg, Litro" required>
                            @error('unidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unidad de Compra -->
                        <div class="col-md-6 mb-3">
                            <label for="unidad_compra" class="form-label">Unidad de Compra <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unidad_compra') is-invalid @enderror" id="unidad_compra" name="unidad_compra" value="{{ old('unidad_compra', $producto->unidad_compra) }}" placeholder="Ej: Caja, Paquete" required>
                            @error('unidad_compra')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contenido -->
                        <div class="col-md-4 mb-3">
                            <label for="contenido" class="form-label">Contenido <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('contenido') is-invalid @enderror" id="contenido" name="contenido" value="{{ old('contenido', $producto->contenido) }}" required>
                            @error('contenido')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock Mínimo -->
                        <div class="col-md-4 mb-3">
                            <label for="stock_min" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock_min') is-invalid @enderror" id="stock_min" name="stock_min" value="{{ old('stock_min', $producto->stock_min) }}" required>
                            @error('stock_min')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock Máximo -->
                        <div class="col-md-4 mb-3">
                            <label for="stock_max" class="form-label">Stock Máximo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('stock_max') is-invalid @enderror" id="stock_max" name="stock_max" value="{{ old('stock_max', $producto->stock_max) }}" required>
                            @error('stock_max')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- precio venta -->
                        <div class="col-md-6 mb-3">
                            <label for="precio_venta" class="form-label">Precio venta <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('precio_venta') is-invalid @enderror" id="precio_venta" name="precio_venta" value="{{ old('precio_venta', $producto->precio_venta) }}" required>
                            @error('precio_venta')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <!-- precio minimo -->
                        <div class="col-md-6 mb-3">
                            <label for="precio_minimo" class="form-label">Precio mínimo <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('precio_minimo') is-invalid @enderror" id="precio_minimo" name="precio_minimo" value="{{ old('precio_minimo', $producto->precio_minimo) }}" required>
                            @error('precio_minimo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="activo" {{ old('status', $producto->status) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('status', $producto->status) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Imagen -->
                        <div class="col-md-6 mb-3">
                            <label for="imagen" class="form-label">Cambiar Imagen</label>
                            <input type="file" class="form-control @error('imagen') is-invalid @enderror" id="imagen" name="imagen" accept="image/*" onchange="previewImage(event)">
                            @error('imagen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                        </div>

                        <!-- Preview de la imagen -->
                        <div class="col-md-12 mb-3">
                            @if($producto->imagen)
                                <div id="currentImage">
                                    <label class="form-label">Imagen actual:</label>
                                    <div>
                                        <img src="{{ asset('storage/productos/' . $producto->imagen) }}" alt="{{ $producto->descripcion }}" class="img-thumbnail" style="max-width: 200px;">
                                    </div>
                                </div>
                            @endif
                            <div id="imagePreview" style="display: none;">
                                <label class="form-label">Nueva imagen:</label>
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
                            <i class="fas fa-save"></i> Actualizar Producto
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
            const currentImage = document.getElementById('currentImage');
            
            preview.src = reader.result;
            imagePreview.style.display = 'block';
            
            if (currentImage) {
                currentImage.style.display = 'none';
            }
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endpush
