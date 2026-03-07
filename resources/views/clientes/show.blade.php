@extends('layouts.app')

@section('title', 'Detalles del Cliente')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('clientes.index') }}">Clientes</a></li>
                <li class="breadcrumb-item active">Detalles del Cliente</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-user"></i> Detalles del Cliente</h5>
                <span class="badge badge-{{ $cliente->status }} fs-6">
                    {{ ucfirst($cliente->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="mb-1">{{ $cliente->nombre }}</h3>
                        @if($cliente->razon_social)
                            <p class="text-muted mb-4">{{ $cliente->razon_social }}</p>
                        @else
                            <p class="mb-4"></p>
                        @endif

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
                                            <p class="fw-bold">{{ $cliente->rfc ? '<code>' . $cliente->rfc . '</code>' : '—' }}</p>
                                        </div>
                                        <div class="info-item">
                                            <label class="text-muted"><i class="fas fa-building"></i> Razón Social:</label>
                                            <p>{{ $cliente->razon_social ?: '—' }}</p>
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
                                            <p class="fw-bold">{{ $cliente->telefono ?: '—' }}</p>
                                        </div>
                                        <div class="info-item">
                                            <label class="text-muted"><i class="fas fa-envelope"></i> Correo:</label>
                                            <p>
                                                @if($cliente->correoe)
                                                    <a href="mailto:{{ $cliente->correoe }}">{{ $cliente->correoe }}</a>
                                                @else
                                                    —
                                                @endif
                                            </p>
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
                                            <div class="col-md-6">
                                                <div class="info-item mb-3">
                                                    <label class="text-muted"><i class="fas fa-map-marker-alt"></i> Domicilio:</label>
                                                    <p class="fw-bold">{{ $cliente->domicilio ?: '—' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-item mb-3">
                                                    <label class="text-muted"><i class="fas fa-city"></i> Ciudad:</label>
                                                    <p class="fw-bold">{{ $cliente->ciudad ?: '—' }}</p>
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="info-item mb-3">
                                                    <label class="text-muted"><i class="fas fa-mail-bulk"></i> C.P.:</label>
                                                    <p class="fw-bold">{{ $cliente->cpostal ?: '—' }}</p>
                                                </div>
                                            </div>
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
                                            <p>#{{ $cliente->id }}</p>
                                        </div>
                                        <div class="info-item mb-3">
                                            <label class="text-muted"><i class="fas fa-calendar-plus"></i> Creado:</label>
                                            <p>{{ $cliente->created_at->format('d/m/Y H:i:s') }}</p>
                                        </div>
                                        <div class="info-item">
                                            <label class="text-muted"><i class="fas fa-calendar-check"></i> Actualizado:</label>
                                            <p>{{ $cliente->updated_at->format('d/m/Y H:i:s') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('clientes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <div>
                        <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>

                <form id="delete-form" action="{{ route('clientes.destroy', $cliente) }}" method="POST" style="display: none;">
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
        if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
