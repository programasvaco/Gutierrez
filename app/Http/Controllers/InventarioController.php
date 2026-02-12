<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\Producto;
use App\Models\Almacen;
use Illuminate\Http\Request;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Inventario::with(['producto', 'almacen']);

        // Filtro por producto
        if ($request->has('producto_id') && $request->producto_id != '') {
            $query->where('producto_id', $request->producto_id);
        }

        // Filtro por almacén
        if ($request->has('almacen_id') && $request->almacen_id != '') {
            $query->where('almacen_id', $request->almacen_id);
        }

        // Búsqueda por código o descripción de producto
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('producto', function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro por existencia
        if ($request->has('tipo_existencia') && $request->tipo_existencia != '') {
            switch ($request->tipo_existencia) {
                case 'con_stock':
                    $query->where('existencia', '>', 0);
                    break;
                case 'sin_stock':
                    $query->where('existencia', '<=', 0);
                    break;
                case 'stock_bajo':
                    $query->whereHas('producto', function($q) {
                        $q->whereRaw('inventario.existencia < productos.stock_min');
                    });
                    break;
            }
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'producto');
        $orderDirection = $request->get('order_direction', 'asc');

        if ($orderBy == 'producto') {
            $query->join('productos', 'inventario.producto_id', '=', 'productos.id')
                  ->select('inventario.*')
                  ->orderBy('productos.descripcion', $orderDirection);
        } elseif ($orderBy == 'almacen') {
            $query->join('almacenes', 'inventario.almacen_id', '=', 'almacenes.id')
                  ->select('inventario.*')
                  ->orderBy('almacenes.nombre', $orderDirection);
        } elseif ($orderBy == 'existencia') {
            $query->orderBy('existencia', $orderDirection);
        }

        // Paginación
        $inventarios = $query->paginate(20)->appends($request->query());

        // Obtener listas para filtros
        $productos = Producto::where('status', 'activo')->orderBy('descripcion')->get();
        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();

        // Estadísticas
        $totalRegistros = Inventario::count();
        $conStock = Inventario::where('existencia', '>', 0)->count();
        $sinStock = Inventario::where('existencia', '<=', 0)->count();

        return view('inventarios.index', compact('inventarios', 'productos', 'almacenes', 'totalRegistros', 'conStock', 'sinStock'));
    }

    /**
     * Reporte de stock bajo
     */
    public function stockBajo(Request $request)
    {
        $query = Inventario::with(['producto', 'almacen'])
            ->join('productos', 'inventario.producto_id', '=', 'productos.id')
            ->whereRaw('inventario.existencia < productos.stock_min')
            ->select('inventario.*');

        // Filtro por almacén
        if ($request->has('almacen_id') && $request->almacen_id != '') {
            $query->where('inventario.almacen_id', $request->almacen_id);
        }

        $inventarios = $query->orderBy('inventario.existencia', 'asc')->paginate(20)->appends($request->query());
        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();

        return view('inventarios.stock-bajo', compact('inventarios', 'almacenes'));
    }

    /**
     * Reporte consolidado por producto
     */
    public function consolidado(Request $request)
    {
        // Obtener todos los productos activos
        $productosQuery = Producto::where('status', 'activo');

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $productosQuery->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        $productos = $productosQuery->orderBy('descripcion')->paginate(20)->appends($request->query());

        // Para cada producto, obtener la suma de existencias
        foreach ($productos as $producto) {
            $producto->existencia_total = Inventario::where('producto_id', $producto->id)->sum('existencia');
            $producto->almacenes_con_stock = Inventario::where('producto_id', $producto->id)
                ->where('existencia', '>', 0)
                ->count();
        }

        return view('inventarios.consolidado', compact('productos'));
    }
}
