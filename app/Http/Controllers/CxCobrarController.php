<?php

namespace App\Http\Controllers;

use App\Models\CxCobrar;
use App\Models\Cliente;
use Illuminate\Http\Request;

class CxCobrarController extends Controller
{
    public function index(Request $request)
    {
        $query = CxCobrar::with(['cliente', 'venta']);

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        if ($request->filled('fecha_desde')) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->filled('fecha_hasta')) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        if ($request->filled('estado')) {
            match ($request->estado) {
                'cobradas'  => $query->where('saldo', '<=', 0),
                'pendientes'=> $query->where('saldo', '>', 0),
                'vencidas'  => $query->where('saldo', '>', 0)->where('fecha_vencimiento', '<', now()),
                default     => null,
            };
        }

        $cuentas   = $query->orderBy('fecha_vencimiento', 'asc')->paginate(20)->withQueryString();
        $clientes  = Cliente::activos()->orderBy('nombre')->get();

        $totalPendiente = CxCobrar::where('saldo', '>', 0)->sum('saldo');
        $totalVencido   = CxCobrar::where('saldo', '>', 0)->where('fecha_vencimiento', '<', now())->sum('saldo');
        $cantidadVencidas = CxCobrar::where('saldo', '>', 0)->where('fecha_vencimiento', '<', now())->count();

        return view('cxcobrar.index', compact('cuentas', 'clientes', 'totalPendiente', 'totalVencido', 'cantidadVencidas'));
    }

    public function vencidas(Request $request)
    {
        $query = CxCobrar::with(['cliente', 'venta'])
            ->where('saldo', '>', 0)
            ->where('fecha_vencimiento', '<', now());

        if ($request->filled('cliente_id')) {
            $query->where('cliente_id', $request->cliente_id);
        }

        $cuentas  = $query->orderBy('fecha_vencimiento', 'asc')->paginate(20)->withQueryString();
        $clientes = Cliente::activos()->orderBy('nombre')->get();
        $totalVencido = CxCobrar::where('saldo', '>', 0)->where('fecha_vencimiento', '<', now())->sum('saldo');

        return view('cxcobrar.vencidas', compact('cuentas', 'clientes', 'totalVencido'));
    }

    public function porCliente(Request $request)
    {
        $clientes   = Cliente::activos()->orderBy('nombre')->get();
        $cliente_id = $request->get('cliente_id');

        if (! $cliente_id) {
            return view('cxcobrar.por-cliente', compact('clientes'));
        }

        $cliente     = Cliente::findOrFail($cliente_id);
        $cuentas     = CxCobrar::with(['venta'])
            ->where('cliente_id', $cliente_id)
            ->orderBy('fecha_vencimiento', 'asc')
            ->paginate(20)
            ->withQueryString();

        $totalImporte = CxCobrar::where('cliente_id', $cliente_id)->sum('importe');
        $totalSaldo   = CxCobrar::where('cliente_id', $cliente_id)->sum('saldo');

        return view('cxcobrar.por-cliente', compact('cuentas', 'cliente', 'clientes', 'totalImporte', 'totalSaldo'));
    }
}
