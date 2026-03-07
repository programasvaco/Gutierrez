@extends('layouts.app')
@section('title', 'Categorías')
@section('content')
<div class="row mb-4">
    <div class="col-md-12"><h2><i class="fas fa-tags"></i> Categorías de Productos</h2></div>
</div>
<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6"><h5 class="mb-0">Lista de Categorías</h5></div>
            <div class="col-md-6 text-end">
                <a href="{{ route('categorias.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('categorias.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-10">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar categoría..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter"></i> Filtrar</button>
                </div>
            </div>
        </form>
        @if($categorias->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th class="text-center">Total Productos</th>
                        <th>Fecha Creación</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categorias as $categoria)
                    <tr>
                        <td><strong>#{{ $categoria->id }}</strong></td>
                        <td>
                            <i class="fas fa-tag text-primary"></i> 
                            <strong>{{ $categoria->nombre }}</strong>
                        </td>
                        <td class="text-center">
                            @if($categoria->productos_count > 0)
                                <span class="badge bg-primary">{{ $categoria->productos_count }}</span>
                            @else
                                <span class="badge bg-secondary">0</span>
                            @endif
                        </td>
                        <td>{{ $categoria->created_at->format('d/m/Y') }}</td>
                        <td class="text-center">
                            <a href="{{ route('categorias.show', $categoria) }}" class="btn btn-sm btn-info" title="Ver">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-warning" title="Editar">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger" title="Eliminar" 
                                        onclick="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="d-flex justify-content-between align-items-center mt-4">
            <div>Mostrando {{ $categorias->firstItem() }} - {{ $categorias->lastItem() }} de {{ $categorias->total() }} categorías</div>
            <div>{{ $categorias->links() }}</div>
        </div>
        @else
        <div class="alert alert-info text-center">
            <i class="fas fa-info-circle"></i> No hay categorías registradas.
            <a href="{{ route('categorias.create') }}" class="alert-link">Crear primera categoría</a>
        </div>
        @endif
    </div>
</div>
@endsection
