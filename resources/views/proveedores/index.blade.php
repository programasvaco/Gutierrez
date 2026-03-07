@extends('layouts.app')
@section('title', 'Proveedores')
@section('content')
<div class="row mb-4">
    <div class="col-md-12"><h2><i class="fas fa-truck"></i> Gestión de Proveedores</h2></div>
</div>
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6"><h5 class="mb-0">Lista de Proveedores</h5></div>
            <div class="col-md-6 text-end">                
                <a href="{{ route('proveedores.import') }}" class="btn btn-success me-2">
                    <i class="fas fa-file-import"></i> Importar Excel
                </a>
                <a href="{{ route('proveedores.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Proveedor
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('proveedores.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nombre, RFC o ciudad..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                    </select>
                </div>
                <div class="col-md-2"><button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filtrar</button></div>
            </div>
        </form>
        @if($proveedores->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light"><tr><th>Nombre</th><th>RFC</th><th>Ciudad</th><th>Contacto</th><th class="text-center">Estado</th><th class="text-center">Acciones</th></tr></thead>
                <tbody>
                    @foreach($proveedores as $proveedor)
                    <tr>
                        <td><strong>{{ $proveedor->nombre }}</strong></td>
                        <td>{{ $proveedor->rfc ?: '-' }}</td>
                        <td>{{ $proveedor->ciudad ?: '-' }}</td>
                        <td>{{ $proveedor->contacto ?: '-' }}</td>
                        <td class="text-center"><span class="badge {{ $proveedor->status_badge }}">{{ ucfirst($proveedor->status) }}</span></td>
                        <td class="text-center">
                            <a href="{{ route('proveedores.show', $proveedor) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('proveedores.edit', $proveedor) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('proveedores.destroy', $proveedor) }}" method="POST" style="display:inline;">@csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <div>Mostrando {{ $proveedores->firstItem() }} - {{ $proveedores->lastItem() }} de {{ $proveedores->total() }}</div>
            <div>{{ $proveedores->links() }}</div>
        </div>
        @else
        <div class="alert alert-info text-center">No hay proveedores. <a href="{{ route('proveedores.import') }}">¿Importar desde Excel?</a></div>
        @endif
    </div>
</div>
@endsection
