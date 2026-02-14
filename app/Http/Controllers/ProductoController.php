<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Producto::query();

        // Búsqueda
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('codigo', 'like', "%{$search}%")
                  ->orWhere('descripcion', 'like', "%{$search}%")
                  ->orWhere('codigo_empaque', 'like', "%{$search}%");
            });
        }

        // Filtro por status
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Filtro por rango de precios
        if ($request->has('precio_min') && $request->precio_min != '') {
            $query->where('precio_venta', '>=', $request->precio_min);
        }

        if ($request->has('precio_max') && $request->precio_max != '') {
            $query->where('precio_venta', '<=', $request->precio_max);
        }

        $productos = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('productos.index', compact('productos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('productos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo',
            'codigo_empaque' => 'nullable|string|max:50',
            'descripcion' => 'required|string|max:255',
            'unidad' => 'required|string|max:50',
            'unidad_compra' => 'required|string|max:50',
            'contenido' => 'required|numeric|min:0',
            'stock_min' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_minimo' => 'required|numeric|min:0',
            'status' => 'required|in:activo,inactivo',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Validación adicional: precio_venta no puede ser menor a precio_minimo
        if ($validated['precio_venta'] < $validated['precio_minimo']) {
            return back()
                ->withInput()
                ->withErrors(['precio_venta' => 'El precio de venta no puede ser menor al precio mínimo.']);
        }

        // Manejar la imagen
        if ($request->hasFile('imagen')) {
            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->storeAs('productos', $nombreImagen, 'public');
            $validated['imagen'] = $nombreImagen;
        }

        Producto::create($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Producto $producto)
    {
        return view('productos.show', compact('producto'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Producto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'codigo' => 'required|string|max:50|unique:productos,codigo,' . $producto->id,
            'codigo_empaque' => 'nullable|string|max:50',
            'descripcion' => 'required|string|max:255',
            'unidad' => 'required|string|max:50',
            'unidad_compra' => 'required|string|max:50',
            'contenido' => 'required|numeric|min:0',
            'stock_min' => 'required|integer|min:0',
            'stock_max' => 'required|integer|min:0',
            'precio_venta' => 'required|numeric|min:0',
            'precio_minimo' => 'required|numeric|min:0',
            'status' => 'required|in:activo,inactivo',
            'imagen' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Validación adicional: precio_venta no puede ser menor a precio_minimo
        if ($validated['precio_venta'] < $validated['precio_minimo']) {
            return back()
                ->withInput()
                ->withErrors(['precio_venta' => 'El precio de venta no puede ser menor al precio mínimo.']);
        }

        // Manejar la imagen
        if ($request->hasFile('imagen')) {
            // Eliminar imagen anterior si existe
            if ($producto->imagen) {
                Storage::disk('public')->delete('productos/' . $producto->imagen);
            }

            $imagen = $request->file('imagen');
            $nombreImagen = time() . '_' . $imagen->getClientOriginalName();
            $imagen->storeAs('productos', $nombreImagen, 'public');
            $validated['imagen'] = $nombreImagen;
        }

        $producto->update($validated);

        return redirect()->route('productos.index')
            ->with('success', 'Producto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Producto $producto)
    {
        // Eliminar imagen si existe
        if ($producto->imagen) {
            Storage::disk('public')->delete('productos/' . $producto->imagen);
        }

        $producto->delete();

        return redirect()->route('productos.index')
            ->with('success', 'Producto eliminado exitosamente.');
    }
}