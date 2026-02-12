<?php

namespace App\Http\Controllers;

use App\Models\Almacen;
use Illuminate\Http\Request;

class AlmacenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Almacen::query();

        // Búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('ciudad', 'like', "%{$search}%")
                  ->orWhere('domicilio', 'like', "%{$search}%");
            });
        }

        // Filtro por status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $almacenes = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('almacenes.index', compact('almacenes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('almacenes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'domicilio' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'status' => 'required|in:activo,inactivo'
        ]);

        Almacen::create($validated);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Almacen $almacene)
    {
        return view('almacenes.show', compact('almacene'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Almacen $almacene)
    {
        return view('almacenes.edit', compact('almacene'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Almacen $almacene)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100',
            'domicilio' => 'required|string|max:255',
            'ciudad' => 'required|string|max:100',
            'status' => 'required|in:activo,inactivo'
        ]);

        $almacene->update($validated);

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Almacen $almacene)
    {
        $almacene->delete();

        return redirect()->route('almacenes.index')
            ->with('success', 'Almacén eliminado exitosamente.');
    }
}
