<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                  ->orWhere('razon_social', 'like', "%{$search}%")
                  ->orWhere('rfc', 'like', "%{$search}%")
                  ->orWhere('ciudad', 'like', "%{$search}%");
            });
        }

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $clientes = $query->orderBy('nombre')->paginate(10);

        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:150|unique:clientes,nombre',
            'razon_social'=> 'nullable|string|max:200',
            'domicilio'   => 'nullable|string|max:255',
            'ciudad'      => 'nullable|string|max:100',
            'cpostal'     => 'nullable|string|max:10',
            'rfc'         => 'nullable|string|max:13',
            'telefono'    => 'nullable|string|max:20',
            'correoe'     => 'nullable|email|max:100',
            'status'      => 'required|in:activo,inactivo',
            'dias_plazo'  => 'required|integer|min:0|max:365',
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente creado exitosamente.');
    }

    public function show(Cliente $cliente)
    {
        return view('clientes.show', compact('cliente'));
    }

    public function edit(Cliente $cliente)
    {
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $request->validate([
            'nombre'      => 'required|string|max:150|unique:clientes,nombre,' . $cliente->id,
            'razon_social'=> 'nullable|string|max:200',
            'domicilio'   => 'nullable|string|max:255',
            'ciudad'      => 'nullable|string|max:100',
            'cpostal'     => 'nullable|string|max:10',
            'rfc'         => 'nullable|string|max:13',
            'telefono'    => 'nullable|string|max:20',
            'correoe'     => 'nullable|email|max:100',
            'status'      => 'required|in:activo,inactivo',
            'dias_plazo'  => 'required|integer|min:0|max:365',
        ]);

        $cliente->update($validated);

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente actualizado exitosamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();

        return redirect()->route('clientes.index')
            ->with('success', 'Cliente eliminado exitosamente.');
    }
}
