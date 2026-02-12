@extends('layouts.app')

@section('title', 'Lista de Productos')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-boxes"></i> Gestión de Productos</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Lista de Productos</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('productos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros y búsqueda -->
        <form action="{{ route('productos.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por código o descripción..." value="{{ request('search') }}">
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

        @if($productos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Imagen</th>
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Unidad</th>
                            <th>Contenido</th>
                            <th>Stock Min/Max</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr>
                            <td>
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/productos/' . $producto->imagen) }}" alt="{{ $producto->descripcion }}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded">
                                @else
                                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $producto->codigo }}</strong>
                                @if($producto->codigo_empaque)
                                    <br><small class="text-muted">Emp: {{ $producto->codigo_empaque }}</small>
                                @endif
                            </td>
                            <td>{{ $producto->descripcion }}</td>
                            <td>
                                {{ $producto->unidad }}
                                <br><small class="text-muted">Compra: {{ $producto->unidad_compra }}</small>
                            </td>
                            <td>{{ $producto->contenido }}</td>
                            <td>
                                <span class="badge bg-info">Min: {{ $producto->stock_min }}</span>
                                <span class="badge bg-warning">Max: {{ $producto->stock_max }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $producto->status }}">
                                    {{ ucfirst($producto->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-warning" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmarEliminacion({{ $producto->id }})" title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <form id="delete-form-{{ $producto->id }}" action="{{ route('productos.destroy', $producto) }}" method="POST" style="display: none;">
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
                {{ $productos->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron productos.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarEliminacion(id) {
        if (confirm('¿Está seguro de que desea eliminar este producto?')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
