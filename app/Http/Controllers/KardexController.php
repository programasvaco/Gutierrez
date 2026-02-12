<?php

namespace App\Http\Controllers;

use App\Models\Kardex;
use App\Models\Producto;
use App\Models\Almacen;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $productos = Producto::where('status', 'activo')->orderBy('descripcion')->get();
        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();
        
        $movimientos = null;
        $productoSeleccionado = null;
        $almacenSeleccionado = null;

        if ($request->has('producto_id') && $request->has('almacen_id')) {
            $query = Kardex::with(['producto', 'almacen'])
                ->where('producto_id', $request->producto_id)
                ->where('almacen_id', $request->almacen_id);

            // Filtros de fecha
            if ($request->has('fecha_desde') && $request->fecha_desde) {
                $query->where('fecha', '>=', $request->fecha_desde);
            }

            if ($request->has('fecha_hasta') && $request->fecha_hasta) {
                $query->where('fecha', '<=', $request->fecha_hasta);
            }

            // Filtro por tipo de documento
            if ($request->has('documento') && $request->documento != '') {
                $query->where('documento', $request->documento);
            }

            // Filtro por tipo de movimiento
            if ($request->has('tipo_movimiento') && $request->tipo_movimiento != '') {
                $query->where('tipo_movimiento', $request->tipo_movimiento);
            }

            $movimientos = $query->orderBy('fecha', 'asc')
                ->orderBy('hora', 'asc')
                ->get();

            $productoSeleccionado = Producto::find($request->producto_id);
            $almacenSeleccionado = Almacen::find($request->almacen_id);
        }

        return view('kardex.index', compact(
            'productos', 
            'almacenes', 
            'movimientos',
            'productoSeleccionado',
            'almacenSeleccionado'
        ));
    }

    /**
     * Reporte general de kardex
     */
    public function reporte(Request $request)
    {
        $query = Kardex::with(['producto', 'almacen']);

        // Filtros
        if ($request->has('almacen_id') && $request->almacen_id) {
            $query->where('almacen_id', $request->almacen_id);
        }

        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $movimientos = $query->orderBy('fecha', 'desc')
            ->orderBy('hora', 'desc')
            ->paginate(50);

        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();

        return view('kardex.reporte', compact('movimientos', 'almacenes'));
    }
}
