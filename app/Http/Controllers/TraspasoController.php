<?php

namespace App\Http\Controllers;

use App\Models\Traspaso;
use App\Models\DetalleTraspaso;
use App\Models\Inventario;
use App\Models\Almacen;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TraspasoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Traspaso::with(['almacenOrigen', 'almacenDestino']);

        // Búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('folio', 'like', "%{$search}%");
        }

        // Filtro por status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filtro por almacén origen
        if ($request->has('almacen_origen_id') && $request->almacen_origen_id) {
            $query->where('almacen_origen_id', $request->almacen_origen_id);
        }

        // Filtro por almacén destino
        if ($request->has('almacen_destino_id') && $request->almacen_destino_id) {
            $query->where('almacen_destino_id', $request->almacen_destino_id);
        }

        // Filtro por fecha
        if ($request->has('fecha_desde') && $request->fecha_desde) {
            $query->where('fecha', '>=', $request->fecha_desde);
        }
        if ($request->has('fecha_hasta') && $request->fecha_hasta) {
            $query->where('fecha', '<=', $request->fecha_hasta);
        }

        $traspasos = $query->orderBy('fecha', 'desc')->orderBy('hora', 'desc')->paginate(10);
        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();

        return view('traspasos.index', compact('traspasos', 'almacenes'));
    }

    /**
     * Lista de traspasos por recibir
     */
    public function porRecibir(Request $request)
    {
        $almacen_id = $request->get('almacen_destino_id');
        
        $query = Traspaso::with(['almacenOrigen', 'almacenDestino', 'detalles'])
            ->pendientesRecibir($almacen_id);

        $traspasos = $query->orderBy('fecha', 'asc')->get();
        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();

        return view('traspasos.por-recibir', compact('traspasos', 'almacenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $almacenes = Almacen::where('status', 'activo')->orderBy('nombre')->get();
        $productos = Producto::where('status', 'activo')->orderBy('descripcion')->get();

        return view('traspasos.create', compact('almacenes', 'productos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'folio' => 'required|string|max:50|unique:traspasos,folio',
            'fecha' => 'required|date',
            'almacen_origen_id' => 'required|exists:almacenes,id',
            'almacen_destino_id' => 'required|exists:almacenes,id|different:almacen_origen_id',
            'observaciones' => 'nullable|string|max:500',
            'detalles' => 'required|array|min:1',
            'detalles.*.producto_id' => 'required|exists:productos,id',
            'detalles.*.cantidad' => 'required|numeric|min:0.01',
            'detalles.*.costo' => 'required|numeric|min:0',
        ]);

        DB::beginTransaction();

        try {
            // Crear el traspaso
            $traspaso = Traspaso::create([
                'folio' => $validated['folio'],
                'fecha' => $validated['fecha'],
                'hora' => now()->toTimeString(),
                'almacen_origen_id' => $validated['almacen_origen_id'],
                'almacen_destino_id' => $validated['almacen_destino_id'],
                'status' => 'creado',
                'observaciones' => $validated['observaciones'] ?? null,
            ]);

            // Crear los detalles
            foreach ($validated['detalles'] as $detalle) {
                DetalleTraspaso::create([
                    'traspaso_id' => $traspaso->id,
                    'producto_id' => $detalle['producto_id'],
                    'cantidad' => $detalle['cantidad'],
                    'costo' => $detalle['costo'],
                ]);
            }

            DB::commit();

            return redirect()->route('traspasos.show', $traspaso)
                ->with('success', 'Traspaso creado exitosamente. Estado: Creado');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el traspaso: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Traspaso $traspaso)
    {
        $traspaso->load(['almacenOrigen', 'almacenDestino', 'detalles.producto']);
        return view('traspasos.show', compact('traspaso'));
    }

    /**
     * Poner traspaso en tránsito
     */
    public function ponerEnTransito(Traspaso $traspaso)
    {
        if (!$traspaso->puedePonerseEnTransito()) {
            return redirect()->back()
                ->with('error', 'El traspaso no puede ponerse en tránsito. Estado actual: ' . $traspaso->status);
        }

        DB::beginTransaction();

        try {
            // Realizar salidas del almacén origen
            foreach ($traspaso->detalles as $detalle) {
                // Verificar existencia suficiente
                $inventario = Inventario::where('almacen_id', $traspaso->almacen_origen_id)
                    ->where('producto_id', $detalle->producto_id)
                    ->first();

                if (!$inventario || $inventario->existencia < $detalle->cantidad) {
                    DB::rollBack();
                    $producto = $detalle->producto;
                    return redirect()->back()
                        ->with('error', "Existencia insuficiente del producto: {$producto->descripcion}. Existencia actual: " . ($inventario->existencia ?? 0));
                }

                // Decrementar inventario y registrar en kardex
                Inventario::decrementarExistencia(
                    $traspaso->almacen_origen_id,
                    $detalle->producto_id,
                    $detalle->cantidad,
                    $detalle->costo,
                    'Salida traspaso',
                    $traspaso->folio,
                    $traspaso->fecha->toDateString()
                );
            }

            // Actualizar status del traspaso
            $traspaso->update([
                'status' => 'en transito',
                'fecha_transito' => now(),
            ]);

            DB::commit();

            return redirect()->route('traspasos.show', $traspaso)
                ->with('success', 'Traspaso puesto en tránsito. Se realizaron las salidas del almacén origen.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al poner en tránsito: ' . $e->getMessage());
        }
    }

    /**
     * Recibir traspaso
     */
    public function recibir(Traspaso $traspaso)
    {
        if (!$traspaso->puedeRecibirse()) {
            return redirect()->back()
                ->with('error', 'El traspaso no puede ser recibido. Estado actual: ' . $traspaso->status);
        }

        DB::beginTransaction();

        try {
            // Realizar entradas al almacén destino
            foreach ($traspaso->detalles as $detalle) {
                Inventario::incrementarExistencia(
                    $traspaso->almacen_destino_id,
                    $detalle->producto_id,
                    $detalle->cantidad,
                    $detalle->costo,
                    'Recepción traspaso',
                    $traspaso->folio,
                    now()->toDateString()
                );
            }

            // Actualizar status del traspaso
            $traspaso->update([
                'status' => 'recibido',
                'fecha_recepcion' => now(),
            ]);

            DB::commit();

            return redirect()->route('traspasos.show', $traspaso)
                ->with('success', 'Traspaso recibido exitosamente. Se realizaron las entradas al almacén destino.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al recibir el traspaso: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar traspaso
     */
    public function cancelar(Traspaso $traspaso)
    {
        if (!$traspaso->puedeCancelarse()) {
            return redirect()->back()
                ->with('error', 'Solo se pueden cancelar traspasos en estado "creado". Estado actual: ' . $traspaso->status);
        }

        DB::beginTransaction();

        try {
            $traspaso->update([
                'status' => 'cancelado',
                'fecha_cancelacion' => now(),
            ]);

            DB::commit();

            return redirect()->route('traspasos.show', $traspaso)
                ->with('success', 'Traspaso cancelado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()
                ->with('error', 'Error al cancelar el traspaso: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Traspaso $traspaso)
    {
        // Solo se pueden eliminar traspasos cancelados o creados
        if (!in_array($traspaso->status, ['creado', 'cancelado'])) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar un traspaso en estado: ' . $traspaso->status);
        }

        $traspaso->delete();

        return redirect()->route('traspasos.index')
            ->with('success', 'Traspaso eliminado exitosamente.');
    }
}
