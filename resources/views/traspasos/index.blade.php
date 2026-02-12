@extends('layouts.app')

@section('title', 'Lista de Traspasos')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-exchange-alt"></i> Gestión de Traspasos</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Lista de Traspasos</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('traspasos.por-recibir') }}" class="btn btn-warning me-2">
                    <i class="fas fa-truck-loading"></i> Por Recibir
                </a>
                <a href="{{ route('traspasos.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nuevo Traspaso
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">
        <!-- Filtros y búsqueda -->
        <form action="{{ route('traspasos.index') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar folio..." value="{{ request('search') }}">
                    </div>
                </div>
                
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Todos los estados</option>
                        <option value="creado" {{ request('status') == 'creado' ? 'selected' : '' }}>Creado</option>
                        <option value="en transito" {{ request('status') == 'en transito' ? 'selected' : '' }}>En Tránsito</option>
                        <option value="recibido" {{ request('status') == 'recibido' ? 'selected' : '' }}>Recibido</option>
                        <option value="cancelado" {{ request('status') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="almacen_origen_id" class="form-select">
                        <option value="">Almacén Origen</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_origen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <select name="almacen_destino_id" class="form-select">
                        <option value="">Almacén Destino</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_destino_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}">
                </div>

                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>
        </form>

        @if($traspasos->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Fecha</th>
                            <th>Folio</th>
                            <th>Origen</th>
                            <th>Destino</th>
                            <th>Productos</th>
                            <th>Estado</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($traspasos as $traspaso)
                        <tr>
                            <td>
                                {{ $traspaso->fecha->format('d/m/Y') }}
                                <br><small class="text-muted">{{ date('H:i', strtotime($traspaso->hora)) }}</small>
                            </td>
                            <td><strong>{{ $traspaso->folio }}</strong></td>
                            <td>
                                <i class="fas fa-warehouse text-danger"></i> {{ $traspaso->almacenOrigen->nombre }}
                            </td>
                            <td>
                                <i class="fas fa-warehouse text-success"></i> {{ $traspaso->almacenDestino->nombre }}
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $traspaso->detalles->count() }} productos</span>
                            </td>
                            <td>
                                <span class="badge {{ $traspaso->status_badge }}">
                                    <i class="fas {{ $traspaso->status_icon }}"></i> {{ ucfirst($traspaso->status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('traspasos.show', $traspaso) }}" class="btn btn-sm btn-info" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="d-flex justify-content-center mt-4">
                {{ $traspasos->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron traspasos.
            </div>
        @endif
    </div>
</div>
@endsection
