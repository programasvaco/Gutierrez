<?php

namespace App\Http\Controllers;

use App\Models\FlujoCaja;
use App\Models\Almacen;
use Illuminate\Http\Request;

class FlujoCajaController extends Controller
{
    public function index(Request $request)
    {
        $query = FlujoCaja::with('almacen');

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('almacen_id')) {
            $query->where('almacen_id', $request->almacen_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $registros = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->paginate(20)->withQueryString();

        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();

        // Totales del período filtrado (sin paginar)
        $queryTotales = FlujoCaja::query();
        if ($request->filled('tipo'))        $queryTotales->where('tipo', $request->tipo);
        if ($request->filled('almacen_id'))  $queryTotales->where('almacen_id', $request->almacen_id);
        if ($request->filled('fecha_desde')) $queryTotales->where('fecha', '>=', $request->fecha_desde);
        if ($request->filled('fecha_hasta')) $queryTotales->where('fecha', '<=', $request->fecha_hasta);

        $totalEntradas = (clone $queryTotales)->where('tipo', 'Entrada')->sum('cantidad');
        $totalSalidas  = (clone $queryTotales)->where('tipo', 'Salida')->sum('cantidad');
        $saldo         = $totalEntradas - $totalSalidas;

        return view('flujo_caja.index', compact('registros', 'almacenes', 'totalEntradas', 'totalSalidas', 'saldo'));
    }
}
