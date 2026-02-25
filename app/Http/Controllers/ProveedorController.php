<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Proveedor::query();

        // Búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('razon_social', 'like', "%{$search}%")
                  ->orWhere('rfc', 'like', "%{$search}%")
                  ->orWhere('ciudad', 'like', "%{$search}%");
            });
        }

        // Filtro por status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $proveedores = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('proveedores.index', compact('proveedores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('proveedores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'razon_social' => 'nullable|string|max:200',
            'rfc' => 'nullable|string|size:13|unique:proveedores,rfc',
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'dias_plazo' => 'nullable|integer|min:0',
            'status' => 'required|in:activo,inactivo'
            
        ]);

        Proveedor::create($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Proveedor $proveedore)
    {
        return view('proveedores.show', compact('proveedore'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Proveedor $proveedore)
    {
        return view('proveedores.edit', compact('proveedore'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Proveedor $proveedore)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:150',
            'razon_social' => 'nullable|string|max:200',
            'rfc' => 'nullable|string|size:13|unique:proveedores,rfc,' . $proveedore->id,
            'direccion' => 'nullable|string|max:255',
            'ciudad' => 'nullable|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'dias_plazo' => 'nullable|integer|min:0',
            'status' => 'required|in:activo,inactivo'
        ]);

        $proveedore->update($validated);

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Proveedor $proveedore)
    {
        $proveedore->delete();

        return redirect()->route('proveedores.index')
            ->with('success', 'Proveedor eliminado exitosamente.');
    }
}
