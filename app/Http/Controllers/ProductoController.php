<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Producto::with('categoria');

        // Búsqueda
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%");
            });
        }

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        $productos = $query->orderBy('descripcion')->paginate(15);
        
        // Obtener categorías para el filtro
        $categorias = Categoria::orderBy('nombre')->get();

        return view('productos.index', compact('productos', 'categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.create', compact('categorias'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'categoria_id' => 'nullable|exists:categorias,id',
            'codigoEmpaque' => 'nullable|string|max:50',
            'descripcion' => 'required|string|max:255',
            'unidad' => 'required|string|max:50',
            'unidad_compra' => 'nullable|string|',
            'contenido' => 'nullable|numeric|min:0',
            'stock_min' => 'required|numeric|min:0',
            'stock_max' => 'required|numeric|min:0',
            'status' => 'required|in:activo,inactivo',
        ]);

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        $producto->load('categoria');
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $producto->id,
            'categoria_id' => 'nullable|exists:categorias,id',
            'codigoEmpaque' => 'nullable|string|max:50',
            'descripcion' => 'required|string|max:255',
            'unidad' => 'required|string|max:50',
            'unidad_compra' => 'nullable|string|max:50',
            'contenido' => 'nullable|numeric|min:0',
            'stock_min' => 'required|numeric|min:0',
            'stock_max' => 'required|numeric|min:0',
            'status' => 'required|in:activo,inactivo',
        ]);

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        // Verificar si tiene inventario
        if ($producto->inventario()->exists()) {
            return redirect()->route('productos.index')
                ->with('error', 'No se puede eliminar el producto porque tiene movimientos de inventario.');
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}
