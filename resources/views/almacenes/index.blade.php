@extends('layouts.app')

@section('title', 'Lista de Almacenes')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-warehouse"></i> Gestión de Almacenes</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Lista de Almacenes</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('almacenes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Almacén
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros y búsqueda -->
        <form action="{{ route('almacenes.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, ciudad o domicilio..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>

        @if($almacenes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Domicilio</th>
                            <th>Ciudad</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($almacenes as $almacen)
                        <tr>
                            <td><strong>#{{ $almacen->id }}</strong></td>
                            <td>{{ $almacen->nombre }}</td>
                            <td>{{ $almacen->domicilio }}</td>
                            <td>
                                <i class="fas fa-map-marker-alt text-danger"></i> {{ $almacen->ciudad }}
                            </td>
                            <td>
                                <span class="badge badge-{{ $almacen->status }}">
                                    {{ ucfirst($almacen->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('almacenes.show', $almacen) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('almacenes.edit', $almacen) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion({{ $almacen->id }})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $almacen->id }}" action="{{ route('almacenes.destroy', $almacen) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $almacenes->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron almacenes.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Está seguro de que desea eliminar este almacén?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
