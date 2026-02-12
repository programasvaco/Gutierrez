@extends('layouts.app')

@section('title', 'Detalles del Proveedor')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('proveedores.index') }}">Proveedores</a></li>
                <li class="breadcrumb-item active">Detalles del Proveedor</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-truck"></i> Detalles del Proveedor</h5>
                <span class="badge badge-{{ $proveedore->status }} fs-6">
                    {{ ucfirst($proveedore->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="mb-1">{{ $proveedore->nombre }}</h3>
                        <p class="text-muted mb-4">{{ $proveedore->razon_social }}</p>

                        <div class="row">
                            <!-- Información fiscal -->
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-file-invoice"></i> Información Fiscal</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-item mb-3">
                                            <label class="text-muted"><i class="fas fa-id-card"></i> RFC:</label>
                                            <p class="fw-bold"><code>{{ $proveedore->rfc }}</code></p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted"><i class="fas fa-building"></i> Razón Social:</label>
                                            <p>{{ $proveedore->razon_social }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información de contacto -->
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-address-book"></i> Información de Contacto</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-item mb-3">
                                            <label class="text-muted"><i class="fas fa-phone"></i> Teléfono:</label>
                                            <p class="fw-bold">{{ $proveedore->telefono }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted"><i class="fas fa-envelope"></i> Correo:</label>
                                            <p><a href="mailto:{{ $proveedore->correo }}">{{ $proveedore->correo }}</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ubicación -->
                            <div class="col-md-12">
                                <div class="card bg-light mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-map-marked-alt"></i> Ubicación</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-8">
                                                <div class="info-item mb-3">
                                                    <label class="text-muted"><i class="fas fa-map-marker-alt"></i> Dirección:</label>
                                                    <p class="fw-bold">{{ $proveedore->direccion }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-4">
                                                <div class="info-item mb-3">
                                                    <label class="text-muted"><i class="fas fa-city"></i> Ciudad:</label>
                                                    <p class="fw-bold">{{ $proveedore->ciudad }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Condiciones comerciales -->
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-handshake"></i> Condiciones Comerciales</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-item mb-3">
                                            <label class="text-muted"><i class="fas fa-calendar-alt"></i> Días de Plazo:</label>
                                            <p>
                                                <span class="badge bg-info fs-6">{{ $proveedore->dias_plazo }} días</span>
                                            </p>
                                        </div>
                                        <div class="info-item">
                                            <label class="text-muted"><i class="fas fa-toggle-on"></i> Estado:</label>
                                            <p>
                                                <span class="badge badge-{{ $proveedore->status }} fs-6">
                                                    {{ ucfirst($proveedore->status) }}
                                                </span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Información del sistema -->
                            <div class="col-md-6">
                                <div class="card bg-light mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Información del Sistema</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="info-item mb-3">
                                            <label class="text-muted"><i class="fas fa-hashtag"></i> ID:</label>
                                            <p>#{{ $proveedore->id }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted"><i class="fas fa-calendar-plus"></i> Creado:</label>
                                            <p>{{ $proveedore->created_at->format('d/m/Y H:i:s') }}</p>
                                        </div>
                                        <div class="info-item">
                                            <label class="text-muted"><i class="fas fa-calendar-check"></i> Actualizado:</label>
                                            <p>{{ $proveedore->updated_at->format('d/m/Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <div>
                        <a href="{{ route('proveedores.edit', $proveedore) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>

                <form id="delete-form" action="{{ route('proveedores.destroy', $proveedore) }}" method="POST" style="display: none;">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .info-item {
        margin-bottom: 0.5rem;
    }
    .info-item label {
        display: block;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    .info-item p {
        margin-bottom: 0;
        font-size: 1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Está seguro de que desea eliminar este proveedor?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
