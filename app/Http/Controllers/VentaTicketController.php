<?php

namespace App\Http\Controllers;

use App\Models\Venta;

class VentaTicketController extends Controller
{
    public function print(Venta $venta)
    {
        $venta->load(['cliente', 'almacen', 'detalles.producto']);

        return view('ventas.ticket', compact('venta'));
    }
}
