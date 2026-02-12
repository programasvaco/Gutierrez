@extends('layouts.app')

@section('title', 'Lista de Proveedores')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-truck"></i> Gestión de Proveedores</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Lista de Proveedores</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros y búsqueda -->
        <form action="{{ route('proveedores.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, razón social, RFC o ciudad..." value="{{ request('search') }}">
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

        @if($proveedores->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>Ciudad</th>
                            <th>Teléfono</th>
                            <th>Días Plazo</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($proveedores as $proveedor)
                        <tr>
                            <td><strong>#{{ $proveedor->id }}</strong></td>
                            <td>
                                <strong>{{ $proveedor->nombre }}</strong>
                                <br><small class="text-muted">{{ $proveedor->razon_social }}</small>
                            </td>
                            <td><code>{{ $proveedor->rfc }}</code></td>
                            <td>
                                <i class="fas fa-map-marker-alt text-danger"></i> {{ $proveedor->ciudad }}
                            </td>
                            <td>
                                <i class="fas fa-phone text-primary"></i> {{ $proveedor->telefono }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $proveedor->dias_plazo }} días</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $proveedor->status }}">
                                    {{ ucfirst($proveedor->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('proveedores.show', $proveedor) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('proveedores.edit', $proveedor) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion({{ $proveedor->id }})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $proveedor->id }}" action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" style="display: none;">
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
                {{ $proveedores->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron proveedores.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Está seguro de que desea eliminar este proveedor?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
