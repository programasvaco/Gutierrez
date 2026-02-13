<?php

namespace App\Http\Controllers;

use App\Models\Traspaso;
use Barryvdh\DomPDF\Facade\Pdf;

class TraspasoPrintController extends Controller
{
    /**
     * Imprimir traspaso individual
     */
    public function print(Traspaso $traspaso)
    {
        $traspaso->load(['almacenOrigen', 'almacenDestino', 'detalles.producto']);
        
        $pdf = PDF::loadView('traspasos.print', compact('traspaso'));
        
        $filename = 'Traspaso_' . $traspaso->folio . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Ver PDF en el navegador (sin descargar)
     */
    public function show(Traspaso $traspaso)
    {
        $traspaso->load(['almacenOrigen', 'almacenDestino', 'detalles.producto']);
        
        $pdf = PDF::loadView('traspasos.print', compact('traspaso'));
        
        return $pdf->stream('Traspaso_' . $traspaso->folio . '.pdf');
    }

    /**
     * Imprimir orden de salida (almacén origen)
     */
    public function printOrdenSalida(Traspaso $traspaso)
    {
        $traspaso->load(['almacenOrigen', 'almacenDestino', 'detalles.producto']);
        
        $pdf = PDF::loadView('traspasos.orden-salida', compact('traspaso'));
        
        $filename = 'Orden_Salida_' . $traspaso->folio . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Imprimir orden de entrada (almacén destino)
     */
    public function printOrdenEntrada(Traspaso $traspaso)
    {
        $traspaso->load(['almacenOrigen', 'almacenDestino', 'detalles.producto']);
        
        $pdf = PDF::loadView('traspasos.orden-entrada', compact('traspaso'));
        
        $filename = 'Orden_Entrada_' . $traspaso->folio . '.pdf';
        
        return $pdf->download($filename);
    }

    /**
     * Imprimir remisión (documento de traslado)
     */
    public function printRemision(Traspaso $traspaso)
    {
        $traspaso->load(['almacenOrigen', 'almacenDestino', 'detalles.producto']);
        
        $pdf = PDF::loadView('traspasos.remision', compact('traspaso'));
        
        $filename = 'Remision_' . $traspaso->folio . '.pdf';
        
        return $pdf->download($filename);
    }
}
