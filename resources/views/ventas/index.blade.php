@extends('layouts.app')

@section('title', 'Ventas')

@section('content')
<div class="row mb-4">
    <div class="col-md-12">
        <h2><i class="fas fa-shopping-bag"></i> Ventas</h2>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="mb-0">Lista de Ventas</h5>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('ventas.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Venta
                </a>
            </div>
        </div>
    </div>
    <div class="card-body">

        <!-- Filtros -->
        <form action="{{ route('ventas.index') }}" method="GET" class="mb-4">
            <div class="row g-2">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Folio o cliente..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="almacen_id" class="form-select">
                        <option value="">Todos los almacenes</option>
                        @foreach($almacenes as $almacen)
                            <option value="{{ $almacen->id }}" {{ request('almacen_id') == $almacen->id ? 'selected' : '' }}>
                                {{ $almacen->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_desde" class="form-control" value="{{ request('fecha_desde') }}" placeholder="Desde">
                </div>
                <div class="col-md-2">
                    <input type="date" name="fecha_hasta" class="form-control" value="{{ request('fecha_hasta') }}" placeholder="Hasta">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filtrar
                    </button>
                </div>
            </div>
        </form>

        @if($ventas->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Folio</th>
                            <th>Fecha</th>
                            <th>Almacén</th>
                            <th>Cliente</th>
                            <th class="text-end">Total</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventas as $venta)
                        <tr>
                            <td><code>{{ $venta->folio }}</code></td>
                            <td>{{ $venta->fecha->format('d/m/Y') }}</td>
                            <td><i class="fas fa-warehouse text-secondary"></i> {{ $venta->almacen->nombre }}</td>
                            <td>
                                @if($venta->cliente)
                                    <i class="fas fa-user text-primary"></i> {{ $venta->cliente->nombre }}
                                @else
                                    <span class="text-muted"><i class="fas fa-store"></i> Mostrador</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">${{ number_format($venta->total, 2) }}</td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('ventas.show', $venta) }}" class="btn btn-sm btn-info" title="Ver">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('ventas.ticket', $venta) }}" target="_blank" class="btn btn-sm btn-dark" title="Imprimir ticket">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" onclick="confirmarCancelacion({{ $venta->id }}, '{{ $venta->folio }}')" title="Cancelar">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <form id="delete-form-{{ $venta->id }}" action="{{ route('ventas.destroy', $venta) }}" method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $ventas->links() }}
            </div>
        @else
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle"></i> No se encontraron ventas.
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmarCancelacion(id, folio) {
        if (confirm('¿Cancelar la venta ' + folio + '?\n\nEsto restaurará las existencias en el almacén.')) {
            document.getElementById('delete-form-' + id).submit();
        }
    }
</script>
@endpush
