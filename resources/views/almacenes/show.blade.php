@extends('layouts.app')

@section('title', 'Detalles del Almacén')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('almacenes.index') }}">Almacenes</a></li>
                <li class="breadcrumb-item active">Detalles del Almacén</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-warehouse"></i> Detalles del Almacén</h5>
                <span class="badge badge-{{ $almacene->status }} fs-6">
                    {{ ucfirst($almacene->status) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3 class="mb-4">{{ $almacene->nombre }}</h3>

                        <div class="info-item mb-4">
                            <label class="text-muted"><i class="fas fa-hashtag"></i> ID:</label>
                            <p class="fw-bold">#{{ $almacene->id }}</p>
                        </div>

                        <div class="info-item mb-4">
                            <label class="text-muted"><i class="fas fa-map-marker-alt"></i> Domicilio:</label>
                            <p class="fw-bold">{{ $almacene->domicilio }}</p>
                        </div>

                        <div class="info-item mb-4">
                            <label class="text-muted"><i class="fas fa-city"></i> Ciudad:</label>
                            <p class="fw-bold">{{ $almacene->ciudad }}</p>
                        </div>

                        <div class="info-item mb-4">
                            <label class="text-muted"><i class="fas fa-toggle-on"></i> Estado:</label>
                            <p>
                                <span class="badge badge-{{ $almacene->status }} fs-6">
                                    {{ ucfirst($almacene->status) }}
                                </span>
                            </p>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-item mb-4">
                                    <label class="text-muted"><i class="fas fa-calendar-plus"></i> Fecha de Creación:</label>
                                    <p>{{ $almacene->created_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item mb-4">
                                    <label class="text-muted"><i class="fas fa-calendar-check"></i> Última Actualización:</label>
                                    <p>{{ $almacene->updated_at->format('d/m/Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between">
                    <a href="{{ route('almacenes.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver al Listado
                    </a>
                    <div>
                        <a href="{{ route('almacenes.edit', $almacene) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <button type="button" class="btn btn-danger" onclick="confirmarEliminacion()">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </div>
                </div>

                <form id="delete-form" action="{{ route('almacenes.destroy', $almacene) }}" method="POST" style="display: none;">
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
        margin-bottom: 1rem;
    }
    .info-item label {
        display: block;
        font-size: 0.9rem;
        margin-bottom: 0.25rem;
    }
    .info-item p {
        margin-bottom: 0;
        font-size: 1.1rem;
    }
</style>
@endpush

@push('scripts')
<script>
    function confirmarEliminacion() {
        if (confirm('¿Está seguro de que desea eliminar este almacén?')) {
            document.getElementById('delete-form').submit();
        }
    }
</script>
@endpush
