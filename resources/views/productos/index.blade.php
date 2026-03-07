@extends('layouts.app')
@section('title', 'Productos')
@section('content')
<div class="row mb-4">
    <div class="col-md-12"><h2><i class="fas fa-box"></i> Gestión de Productos</h2></div>
</div>
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6"><h5 class="mb-0">Lista de Productos</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('productos.import') }}" class="btn btn-success me-2"><i class="fas fa-file-import"></i> Importar</a>
                <a href="{{ route('productos.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo</a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('productos.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="categoria_id" class="form-select">
                        <option value="">Todas las categorías</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
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
        @if($productos->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Código</th>
                        <th>Categoría</th>
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
                            @if($producto->categoria)
                                <span class="badge bg-info">{{ $producto->categoria->nombre }}</span>
                            @else
                                <span class="badge bg-secondary">Sin categoría</span>
                            @endif
                        </td>
                        <td>{{ $producto->descripcion }}</td>
                        <td>{{ $producto->unidad }}</td>
                        <td class="text-end">{{ number_format($producto->stock_min, 2) }}</td>
                        <td class="text-end">{{ number_format($producto->stock_max, 2) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $producto->status == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($producto->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('productos.show', $producto) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('productos.edit', $producto) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('productos.destroy', $producto) }}" method="POST" style="display:inline;">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar?')"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between mt-4">
            <div>Mostrando {{ $productos->firstItem() }} - {{ $productos->lastItem() }} de {{ $productos->total() }}</div>
            <div>{{ $productos->links() }}</div>
        </div>
        @else
        <div class="alert alert-info text-center">No hay productos.</div>
        @endif
    </div>
</div>
@endsection
