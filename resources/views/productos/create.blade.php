@extends('layouts.app')
@section('title', 'Nuevo Producto')
@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('productos.index') }}">Productos</a></li>
                <li class="breadcrumb-item active">Nuevo</li>
            </ol>
        </nav>
    </div>
</div>
<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header"><h5 class="mb-0"><i class="fas fa-plus"></i> Nuevo Producto</h5></div>
            <div class="card-body">
                <form action="{{ route('productos.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo') }}" required maxlength="50">
                            @error('codigo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="categoria_id" class="form-label">Categoría</label>
                            <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id" name="categoria_id">
                                <option value="">Sin categoría</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('descripcion') is-invalid @enderror" id="descripcion" name="descripcion" value="{{ old('descripcion') }}" required maxlength="255">
                        @error('descripcion')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="unidad" class="form-label">Unidad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('unidad') is-invalid @enderror" id="unidad" name="unidad" value="{{ old('unidad') }}" required maxlength="50" placeholder="Ej: Pieza, Kg">
                            @error('unidad')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="unidad_compra" class="form-label">Unidad Compra</label>
                            <input type="text" class="form-control @error('unidad_compra') is-invalid @enderror" id="unidad_compra" name="unidad_compra" value="{{ old('unidad_compra') }}" maxlength="50" placeholder="Ej: Caja">
                            @error('unidad_compra')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="contenido" class="form-label">Contenido</label>
                            <input type="number" step="0.01" class="form-control @error('contenido') is-invalid @enderror" id="contenido" name="contenido" value="{{ old('contenido') }}">
                            @error('contenido')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="stock_min" class="form-label">Stock Mínimo <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('stock_min') is-invalid @enderror" id="stock_min" name="stock_min" value="{{ old('stock_min') }}" required>
                            @error('stock_min')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="stock_max" class="form-label">Stock Máximo <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" class="form-control @error('stock_max') is-invalid @enderror" id="stock_max" name="stock_max" value="{{ old('stock_max') }}" required>
                            @error('stock_max')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="activo" {{ old('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('productos.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Cancelar</a>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
