@extends('layouts.app')

@section('title', 'Crear Almacén')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('almacenes.index') }}">Almacenes</a></li>
                <li class="breadcrumb-item active">Crear Almacén</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-plus-circle"></i> Crear Nuevo Almacén</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('almacenes.store') }}" method="POST">
                    @csrf

                    <!-- Nombre -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label">Nombre del Almacén <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" required>
                        @error('nombre')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Domicilio -->
                    <div class="mb-3">
                        <label for="domicilio" class="form-label">Domicilio <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('domicilio') is-invalid @enderror" id="domicilio" name="domicilio" value="{{ old('domicilio') }}" placeholder="Calle, número, colonia..." required>
                        @error('domicilio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Ciudad -->
                    <div class="mb-3">
                        <label for="ciudad" class="form-label">Ciudad <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad" value="{{ old('ciudad') }}" required>
                        @error('ciudad')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Estado -->
                    <div class="mb-3">
                        <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="activo" {{ old('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="inactivo" {{ old('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('almacenes.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Almacén
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
