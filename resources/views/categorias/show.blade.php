@extends('layouts.app')
@section('title', 'Detalle de Categoría')
@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('categorias.index') }}">Categorías</a></li>
                <li class="breadcrumb-item active">{{ $categoria->nombre }}</li>
            </ol>
        </nav>
    </div>
</div>
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-tag"></i> {{ $categoria->nombre }}
                </h5>
            </div>
            <div class="card-body">
                <h6 class="text-muted mb-3">Productos en esta categoría</h6>
                @if($categoria->productos->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Descripción</th>
                                <th>Unidad</th>
                                <th class="text-center">Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categoria->productos as $producto)
                            <tr>
                                <td><code>{{ $producto->codigo }}</code></td>
                                <td>{{ $producto->descripcion }}</td>
                                <td>{{ $producto->unidad }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $producto->status == 'activo' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($producto->status) }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No hay productos en esta categoría.
                </div>
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-info-circle"></i> Información</h6></div>
            <div class="card-body">
                <p><strong>ID:</strong> {{ $categoria->id }}</p>
                <p><strong>Nombre:</strong> {{ $categoria->nombre }}</p>
                <p><strong>Total Productos:</strong> <span class="badge bg-primary">{{ $categoria->productos->count() }}</span></p>
                <p><strong>Creada:</strong> {{ $categoria->created_at->format('d/m/Y H:i') }}</p>
                <p class="mb-0"><strong>Actualizada:</strong> {{ $categoria->updated_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-header"><h6 class="mb-0"><i class="fas fa-cog"></i> Acciones</h6></div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100" 
                                onclick="return confirm('¿Eliminar esta categoría?')">
                            <i class="fas fa-trash"></i> Eliminar
                        </button>
                    </form>
                    <a href="{{ route('categorias.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection