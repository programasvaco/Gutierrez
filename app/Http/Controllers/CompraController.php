<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Inventario;
use App\Models\CxPagar;
use App\Models\Proveedor;
use App\Models\Almacen;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Compra::with(['proveedor', 'almacen']);

        // Búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('referencia', 'like', "%{$search}%")
                  ->orWhereHas('proveedor', function($q2) use ($search) {
                      $q2->where('nombre', 'like', "%{$search}%");
                  });
            });
        }

        // Filtro por fecha
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $compras = $query->orderBy('fecha', 'desc')->paginate(10);

        return view('compras.index', compact('compras'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $proveedores = Proveedor::where('status', 'activo')->orderBy('nombre')->get();
        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();
        $productos = Producto::where('status', 'activo')->orderBy('descripcion')->get();

        return view('compras.create', compact('proveedores', 'almacenes', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha' => 'required|date',
            'referencia' => 'required|string|max:50|unique:compras,referencia',
            'proveedor_id' => 'required|exists:proveedores,id',
            'almacen_id' => 'required|exists:almacenes,id',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.costo' => 'required|numeric|min:0',
            'detalles.*.impuestos' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Calcular totales
            $subtotal = 0;
            $impuestos = 0;

            foreach ($validated['detalles'] as $detalle) {
                $subtotalDetalle = $detalle['cantidad'] * $detalle['costo'];
                $subtotal += $subtotalDetalle;
                $impuestos += $detalle['impuestos'];
            }

            $total = $subtotal + $impuestos;

            // Crear la compra
            $compra = Compra::create([
                'fecha' => $validated['fecha'],
                'referencia' => $validated['referencia'],
                'proveedor_id' => $validated['proveedor_id'],
                'almacen_id' => $validated['almacen_id'],
                'subtotal' => $subtotal,
                'impuestos' => $impuestos,
                'total' => $total,
            ]);

            // Crear los detalles y afectar inventario con kardex
            foreach ($validated['detalles'] as $detalle) {
                $subtotalDetalle = $detalle['cantidad'] * $detalle['costo'];
                
                DetalleCompra::create([
                    'compra_id' => $compra->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'costo' => $detalle['costo'],
                    'impuestos' => $detalle['impuestos'],
                    'subtotal' => $subtotalDetalle,
                ]);

                // Incrementar inventario Y registrar en kardex
                Inventario::incrementarExistencia(
                    $validated['almacen_id'],
                    $detalle['producto_id'],
                    $detalle['cantidad'],
                    $detalle['costo'],
                    'Compra',
                    $validated['referencia'],
                    $validated['fecha']
                );
            }

            // Crear cuenta por pagar
            $proveedor = Proveedor::find($validated['proveedor_id']);
            $fechaVencimiento = date('Y-m-d', strtotime($validated['fecha'] . ' + ' . $proveedor->dias_plazo . ' days'));

            CxPagar::create([
                'proveedor_id' => $validated['proveedor_id'],
                'compra_id' => $compra->id,
                'fecha' => $validated['fecha'],
                'fecha_vencimiento' => $fechaVencimiento,
                'importe' => $total,
                'saldo' => $total,
            ]);

            DB::commit();

            return redirect()->route('compras.show', $compra)
                ->with('success', 'Compra registrada exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al registrar la compra: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'almacen', 'detalles.producto', 'cuentaPorPagar']);
        return view('compras.show', compact('compra'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Compra $compra)
    {
        // Por seguridad, no permitir editar compras
        return redirect()->route('compras.show', $compra)
            ->with('error', 'No se permite editar compras. Debe eliminar y crear una nueva.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Compra $compra)
    {
        // No permitir actualizar
        return redirect()->route('compras.show', $compra)
            ->with('error', 'No se permite editar compras.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Compra $compra)
    {
        DB::beginTransaction();

        try {
            // Reversar el inventario Y registrar en kardex
            foreach ($compra->detalles as $detalle) {
                Inventario::decrementarExistencia(
                    $compra->almacen_id,
                    $detalle->producto_id,
                    $detalle->cantidad,
                    $detalle->costo,
                    'Cancelación de compra',
                    $compra->referencia,
                    $compra->fecha->toDateString()
                );
            }

            // Eliminar la compra (esto eliminará en cascada detalles y cxpagar)
            $compra->delete();

            DB::commit();

            return redirect()->route('compras.index')
                ->with('success', 'Compra eliminada y movimientos de inventario revertidos.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al eliminar la compra: ' . $e->getMessage());
        }
    }
}
