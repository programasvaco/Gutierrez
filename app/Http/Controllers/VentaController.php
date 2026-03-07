<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Inventario;
use App\Models\Almacen;
use App\Models\Cliente;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $query = Venta::with(['almacen', 'cliente']);

        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                  ->orWhereHas('cliente', fn($q2) => $q2->where('nombre', 'like', "%{$search}%"));
            });
        }

        if ($request->has('almacen_id') && $request->almacen_id) {
            $query->where('almacen_id', $request->almacen_id);
        }

        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $ventas    = $query->orderBy('fecha', 'desc')->orderBy('id', 'desc')->paginate(15);
        $almacenes = Almacen::conVentas()->orderBy('nombre')->get();

        return view('ventas.index', compact('ventas', 'almacenes'));
    }

    public function create()
    {
        $almacenes = Almacen::conVentas()->orderBy('nombre')->get();
        $clientes  = Cliente::activos()->orderBy('nombre')->get();

        return view('ventas.create', compact('almacenes', 'clientes'));
    }

    /**
     * AJAX: devuelve productos con existencia en el almacén solicitado.
     */
    public function productosAlmacen(Almacen $almacen)
    {
        $productos = Inventario::with('producto')
            ->where('almacen_id', $almacen->id)
            ->where('existencia', '>', 0)
            ->get()
            ->map(fn($inv) => [
                'id'          => $inv->producto->id,
                'codigo'      => $inv->producto->codigo,
                'descripcion' => $inv->producto->descripcion,
                'unidad'      => $inv->producto->unidad,
                'precio_venta'=> (float) $inv->producto->precio_venta,
                'existencia'  => (float) $inv->existencia,
            ]);

        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha'                        => 'required|date',
            'almacen_id'                   => 'required|exists:almacenes,id',
            'cliente_id'                   => 'nullable|exists:clientes,id',
            'detalles'                     => 'required|array|min:1',
            'detalles.*.producto_id'       => 'required|exists:productos,id',
            'detalles.*.cantidad'          => 'required|numeric|min:0.01',
            'detalles.*.precio'            => 'required|numeric|min:0',
        ]);

        // Verificar que el almacén permite ventas
        $almacen = Almacen::findOrFail($validated['almacen_id']);
        if (! $almacen->permite_ventas) {
            return back()->withInput()->with('error', 'El almacén seleccionado no permite ventas.');
        }

        // Verificar existencia suficiente para cada producto
        foreach ($validated['detalles'] as $detalle) {
            $inv = Inventario::where('almacen_id', $validated['almacen_id'])
                ->where('producto_id', $detalle['producto_id'])
                ->first();

            $existencia = $inv ? (float) $inv->existencia : 0;

            if ($detalle['cantidad'] > $existencia) {
                $producto = Producto::find($detalle['producto_id']);
                return back()->withInput()->with(
                    'error',
                    "Sin existencia suficiente para «{$producto->descripcion}». Disponible: {$existencia}"
                );
            }
        }

        DB::beginTransaction();

        try {
            $folio    = Venta::generarFolio();
            $subtotal = 0;

            foreach ($validated['detalles'] as $d) {
                $subtotal += $d['cantidad'] * $d['precio'];
            }

            $venta = Venta::create([
                'folio'       => $folio,
                'fecha'       => $validated['fecha'],
                'almacen_id'  => $validated['almacen_id'],
                'cliente_id'  => $validated['cliente_id'] ?? null,
                'subtotal'    => $subtotal,
                'total'       => $subtotal,
            ]);

            foreach ($validated['detalles'] as $d) {
                $sub = $d['cantidad'] * $d['precio'];

                DetalleVenta::create([
                    'venta_id'   => $venta->id,
                    'producto_id'=> $d['producto_id'],
                    'cantidad'   => $d['cantidad'],
                    'precio'     => $d['precio'],
                    'subtotal'   => $sub,
                ]);

                Inventario::decrementarExistencia(
                    $validated['almacen_id'],
                    $d['producto_id'],
                    $d['cantidad'],
                    $d['precio'],
                    'Venta',
                    $folio,
                    $validated['fecha']
                );
            }

            DB::commit();

            return redirect()->route('ventas.show', $venta)
                ->with('success', "Venta {$folio} registrada exitosamente.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar la venta: ' . $e->getMessage());
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['almacen', 'cliente', 'detalles.producto']);

        return view('ventas.show', compact('venta'));
    }

    public function edit(Venta $venta)
    {
        return redirect()->route('ventas.show', $venta)
            ->with('error', 'No se permite editar ventas. Cancele y registre una nueva.');
    }

    public function update(Request $request, Venta $venta)
    {
        return redirect()->route('ventas.show', $venta)
            ->with('error', 'No se permite editar ventas.');
    }

    public function destroy(Venta $venta)
    {
        DB::beginTransaction();

        try {
            foreach ($venta->detalles as $detalle) {
                Inventario::incrementarExistencia(
                    $venta->almacen_id,
                    $detalle->producto_id,
                    $detalle->cantidad,
                    $detalle->precio,
                    'Cancelación de venta',
                    $venta->folio,
                    $venta->fecha->toDateString()
                );
            }

            $venta->delete();

            DB::commit();

            return redirect()->route('ventas.index')
                ->with('success', 'Venta cancelada y existencias restauradas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al cancelar la venta: ' . $e->getMessage());
        }
    }
}
