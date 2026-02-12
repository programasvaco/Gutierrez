<?php

namespace App\Http\Controllers;

use App\Models\CxPagar;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class CxPagarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = CxPagar::with(['proveedor', 'compra']);

        // Filtro por proveedor
        if ($request->has('proveedor_id') && $request->proveedor_id != '') {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        // Filtro por rango de fechas
        if ($request->has('fecha_desde') && $request->fecha_desde != '') {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        if ($request->has('fecha_hasta') && $request->fecha_hasta != '') {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        // Filtro por estado
        if ($request->has('estado') && $request->estado != '') {
            switch ($request->estado) {
                case 'pagadas':
                    $query->where('saldo', '<=', 0);
                    break;
                case 'pendientes':
                    $query->where('saldo', '>', 0);
                    break;
                case 'vencidas':
                    $query->where('saldo', '>', 0)
                          ->where('fecha_vencimiento', '<', now());
                    break;
            }
        }

        // Búsqueda por referencia de compra
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->whereHas('compra', function($q) use ($search) {
                $q->where('referencia', 'like', "%{$search}%");
            });
        }

        // Ordenamiento
        $orderBy = $request->get('order_by', 'fecha_vencimiento');
        $orderDirection = $request->get('order_direction', 'asc');

        switch ($orderBy) {
            case 'proveedor':
                $query->join('proveedores', 'cxpagar.proveedor_id', '=', 'proveedores.id')
                      ->select('cxpagar.*')
                      ->orderBy('proveedores.nombre', $orderDirection);
                break;
            case 'fecha':
                $query->orderBy('fecha', $orderDirection);
                break;
            case 'fecha_vencimiento':
                $query->orderBy('fecha_vencimiento', $orderDirection);
                break;
            case 'saldo':
                $query->orderBy('saldo', $orderDirection);
                break;
            case 'importe':
                $query->orderBy('importe', $orderDirection);
                break;
            default:
                $query->orderBy('fecha_vencimiento', 'asc');
        }

        // Paginación
        $cuentas = $query->paginate(20)->appends($request->query());

        // Proveedores para filtro
        $proveedores = Proveedor::where('status', 'activo')->orderBy('nombre')->get();

        // Estadísticas
        $totalPendiente = CxPagar::where('saldo', '>', 0)->sum('saldo');
        $totalVencido = CxPagar::where('saldo', '>', 0)
            ->where('fecha_vencimiento', '<', now())
            ->sum('saldo');
        $cantidadVencidas = CxPagar::where('saldo', '>', 0)
            ->where('fecha_vencimiento', '<', now())
            ->count();

        return view('cxpagar.index', compact('cuentas', 'proveedores', 'totalPendiente', 'totalVencido', 'cantidadVencidas'));
    }

    /**
     * Reporte de cuentas vencidas
     */
    public function vencidas(Request $request)
    {
        $query = CxPagar::with(['proveedor', 'compra'])
            ->where('saldo', '>', 0)
            ->where('fecha_vencimiento', '<', now());

        // Filtro por proveedor
        if ($request->has('proveedor_id') && $request->proveedor_id != '') {
            $query->where('proveedor_id', $request->proveedor_id);
        }

        $cuentas = $query->orderBy('fecha_vencimiento', 'asc')->paginate(20)->appends($request->query());
        $proveedores = Proveedor::where('status', 'activo')->orderBy('nombre')->get();

        $totalVencido = CxPagar::where('saldo', '>', 0)
            ->where('fecha_vencimiento', '<', now())
            ->sum('saldo');

        return view('cxpagar.vencidas', compact('cuentas', 'proveedores', 'totalVencido'));
    }

    /**
     * Reporte por proveedor
     */
    public function porProveedor(Request $request)
    {
        $proveedores = Proveedor::where('status', 'activo')->orderBy('nombre')->get();
        
        $proveedor_id = $request->get('proveedor_id');

        if (!$proveedor_id) {
            return view('cxpagar.por-proveedor', compact('proveedores'));
        }

        $proveedor = Proveedor::findOrFail($proveedor_id);
        
        $cuentas = CxPagar::with(['compra'])
            ->where('proveedor_id', $proveedor_id)
            ->orderBy('fecha_vencimiento', 'asc')
            ->paginate(20)
            ->appends($request->query());

        $totalImporte = CxPagar::where('proveedor_id', $proveedor_id)->sum('importe');
        $totalSaldo = CxPagar::where('proveedor_id', $proveedor_id)->sum('saldo');

        return view('cxpagar.por-proveedor', compact('cuentas', 'proveedor', 'proveedores', 'totalImporte', 'totalSaldo'));
    }
}
