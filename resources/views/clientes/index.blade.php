@extends('layouts.app')

@section('title', 'Lista de Clientes')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-users"></i> Gestión de Clientes</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Lista de Clientes</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('clientes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Cliente
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros y búsqueda -->
        <form action="{{ route('clientes.index') }}" method="GET" class="mb-4">
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

        @if($clientes->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>RFC</th>
                            <th>Ciudad</th>
                            <th>Teléfono</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($clientes as $cliente)
                        <tr>
                            <td><strong>#{{ $cliente->id }}</strong></td>
                            <td>
                                <strong>{{ $cliente->nombre }}</strong>
                                @if($cliente->razon_social)
                                    <br><small class="text-muted">{{ $cliente->razon_social }}</small>
                                @endif
                            </td>
                            <td>{{ $cliente->rfc ? '<code>' . $cliente->rfc . '</code>' : '—' }}</td>
                            <td>
                                @if($cliente->ciudad)
                                    <i class="fas fa-map-marker-alt text-danger"></i> {{ $cliente->ciudad }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                @if($cliente->telefono)
                                    <i class="fas fa-phone text-primary"></i> {{ $cliente->telefono }}
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $cliente->status }}">
                                    {{ ucfirst($cliente->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('clientes.show', $cliente) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion({{ $cliente->id }})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $cliente->id }}" action="{{ route('clientes.destroy', $cliente) }}" method="POST" style="display: none;">
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
                {{ $clientes->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron clientes.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Está seguro de que desea eliminar este cliente?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
