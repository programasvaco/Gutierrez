@extends('layouts.app')

@section('title', 'Productos')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-box"></i> Gestión de Productos</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Lista de Productos</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('productos.import') }}" class="btn btn-success me-2">
                    <i class="fas fa-file-import"></i> Importar Excel
                </a>
                <a href="{{ route('productos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Producto
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros de búsqueda -->
        <form action="{{ route('productos.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por código o descripción..." value="{{ request('search') }}">
                    </div>
                </div>

                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="activo" {{ request('status') == 'activo' ? 'selected' : '' }}>Activos</option>
                        <option value="inactivo" {{ request('status') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
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
                            <th>Código</th>
                            <th>Descripción</th>
                            <th>Unidad</th>
                            <th class="text-end">Stock Min</th>
                            <th class="text-end">Stock Max</th>
                            <th class="text-center">Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productos as $producto)
                        <tr>
                            <td><code>{{ $producto->codigo }}</code></td>
                            <td>
                                @if($producto->imagen)
                                    <img src="{{ asset('storage/' . $producto->imagen) }}" alt="{{ $producto->descripcion }}" class="img-thumbnail me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                <strong>{{ $producto->descripcion }}</strong>
                            </td>
                            <td>{{ $producto->unidad }}</td>
                            <td class="text-end">{{ number_format($producto->stock_min, 2) }}</td>
                            <td class="text-end">{{ number_format($producto->stock_max, 2) }}</td>
                            <td class="text-center">
                                <span class="badge {{ $producto->status == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($producto->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" onclick="return confirm('¿Estás seguro de eliminar este producto?')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    Mostrando {{ $productos->firstItem() }} - {{ $productos->lastItem() }} de {{ $productos->total() }} productos
                </div>
                <div>
                    {{ $productos->links() }}
                </div>
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron productos. 
                <a href="{{ route('productos.import') }}" class="alert-link">¿Deseas importar desde Excel?</a>
            </div>
        @endif
    </div>
</div>
@endsection
