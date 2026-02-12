@extends('layouts.app')

@section('title', 'Editar Proveedor')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('proveedores.index') }}">Proveedores</a></li>
                <li class="breadcrumb-item active">Editar Proveedor</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Editar Proveedor: {{ $proveedore->nombre }}</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('proveedores.update', $proveedore) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <!-- Nombre -->
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre Comercial <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre', $proveedore->nombre) }}" required>
                            @error('nombre')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Razón Social -->
                        <div class="col-md-6 mb-3">
                            <label for="razon_social" class="form-label">Razón Social <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('razon_social') is-invalid @enderror" id="razon_social" name="razon_social" value="{{ old('razon_social', $proveedore->razon_social) }}" required>
                            @error('razon_social')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- RFC -->
                        <div class="col-md-6 mb-3">
                            <label for="rfc" class="form-label">RFC <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('rfc') is-invalid @enderror" id="rfc" name="rfc" value="{{ old('rfc', $proveedore->rfc) }}" maxlength="13" placeholder="13 caracteres" required style="text-transform: uppercase;">
                            @error('rfc')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Debe contener exactamente 13 caracteres</small>
                        </div>

                        <!-- Ciudad -->
                        <div class="col-md-6 mb-3">
                            <label for="ciudad" class="form-label">Ciudad <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('ciudad') is-invalid @enderror" id="ciudad" name="ciudad" value="{{ old('ciudad', $proveedore->ciudad) }}" required>
                            @error('ciudad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Dirección -->
                        <div class="col-md-12 mb-3">
                            <label for="direccion" class="form-label">Dirección Completa <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('direccion') is-invalid @enderror" id="direccion" name="direccion" value="{{ old('direccion', $proveedore->direccion) }}" placeholder="Calle, número, colonia, código postal" required>
                            @error('direccion')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Teléfono -->
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control @error('telefono') is-invalid @enderror" id="telefono" name="telefono" value="{{ old('telefono', $proveedore->telefono) }}" placeholder="10 dígitos" required>
                            @error('telefono')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Correo -->
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('correo') is-invalid @enderror" id="correo" name="correo" value="{{ old('correo', $proveedore->correo) }}" required>
                            @error('correo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Días de Plazo -->
                        <div class="col-md-6 mb-3">
                            <label for="dias_plazo" class="form-label">Días de Plazo <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('dias_plazo') is-invalid @enderror" id="dias_plazo" name="dias_plazo" value="{{ old('dias_plazo', $proveedore->dias_plazo) }}" min="0" required>
                            @error('dias_plazo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Días de crédito otorgados por el proveedor</small>
                        </div>

                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Estado <span class="text-danger">*</span></label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="activo" {{ old('status', $proveedore->status) == 'activo' ? 'selected' : '' }}>Activo</option>
                                <option value="inactivo" {{ old('status', $proveedore->status) == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <hr>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Actualizar Proveedor
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
    // Convertir RFC a mayúsculas automáticamente
    document.getElementById('rfc').addEventListener('input', function(e) {
        e.target.value = e.target.value.toUpperCase();
    });
</script>
@endpush
