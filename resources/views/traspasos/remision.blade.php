<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Remisión {{ $traspaso->folio }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 11px; padding: 15px; }
        .header { text-align: center; margin-bottom: 15px; border: 2px solid #667eea; padding: 10px; }
        .header h1 { color: #667eea; font-size: 20px; margin-bottom: 5px; }
        .info-grid { display: table; width: 100%; margin-bottom: 15px; }
        .info-cell { display: table-cell; width: 33.33%; padding: 5px; vertical-align: top; }
        .info-cell p { margin: 3px 0; font-size: 10px; }
        .info-cell strong { color: #667eea; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 10px; }
        table thead { background-color: #667eea; color: white; }
        table th { padding: 6px; text-align: left; }
        table td { padding: 5px; border-bottom: 1px solid #ddd; }
        .text-right { text-align: right; }
        .route-box { border: 2px dashed #ffc107; padding: 10px; margin: 15px 0; background-color: #fffbea; }
        .route-box h4 { color: #856404; margin-bottom: 8px; }
        .signatures { display: table; width: 100%; margin-top: 30px; }
        .sig-cell { display: table-cell; width: 33.33%; text-align: center; padding: 10px; }
        .sig-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 9px; }
        .footer { margin-top: 20px; text-align: center; font-size: 9px; color: #666; border-top: 1px solid #ddd; padding-top: 10px; }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>📋 REMISIÓN DE TRASPASO</h1>
        <p style="font-size: 14px; margin-top: 5px;"><strong>{{ $traspaso->folio }}</strong></p>
    </div>

    <!-- Información General -->
    <div class="info-grid">
        <div class="info-cell">
            <p><strong>Fecha de Emisión:</strong></p>
            <p>{{ $traspaso->fecha->format('d/m/Y') }}</p>
            <p>{{ date('H:i', strtotime($traspaso->hora)) }}</p>
        </div>
        <div class="info-cell">
            <p><strong>Estado:</strong></p>
            <p style="font-weight: bold; color: 
                @if($traspaso->status == 'creado') #6c757d
                @elseif($traspaso->status == 'en transito') #ffc107
                @elseif($traspaso->status == 'recibido') #28a745
                @else #dc3545 @endif;">
                {{ strtoupper($traspaso->status) }}
            </p>
        </div>
        <div class="info-cell">
            <p><strong>Valor Total:</strong></p>
            <p style="font-size: 14px; font-weight: bold;">
                ${{ number_format($traspaso->detalles->sum(function($d) { return $d->cantidad * $d->costo; }), 2) }}
            </p>
        </div>
    </div>

    <!-- Ruta -->
    <div class="route-box">
        <h4>🚚 Ruta de Traslado</h4>
        <div style="display: table; width: 100%;">
            <div style="display: table-cell; width: 45%;">
                <p><strong>ORIGEN:</strong></p>
                <p>{{ $traspaso->almacenOrigen->nombre }}</p>
                <p style="font-size: 9px;">{{ $traspaso->almacenOrigen->domicilio }}</p>
                <p style="font-size: 9px;">{{ $traspaso->almacenOrigen->ciudad }}</p>
            </div>
            <div style="display: table-cell; width: 10%; text-align: center; vertical-align: middle;">
                <p style="font-size: 20px;">→</p>
            </div>
            <div style="display: table-cell; width: 45%;">
                <p><strong>DESTINO:</strong></p>
                <p>{{ $traspaso->almacenDestino->nombre }}</p>
                <p style="font-size: 9px;">{{ $traspaso->almacenDestino->domicilio }}</p>
                <p style="font-size: 9px;">{{ $traspaso->almacenDestino->ciudad }}</p>
            </div>
        </div>
    </div>

    <!-- Productos -->
    <table>
        <thead>
            <tr>
                <th style="width: 8%;">No.</th>
                <th style="width: 15%;">Código</th>
                <th style="width: 42%;">Descripción</th>
                <th style="width: 10%;">Unidad</th>
                <th style="width: 12%; text-align: right;">Cantidad</th>
                <th style="width: 13%; text-align: right;">Costo Unit.</th>
            </tr>
        </thead>
        <tbody>
            @foreach($traspaso->detalles as $index => $detalle)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $detalle->producto->codigo }}</strong></td>
                <td>{{ $detalle->producto->descripcion }}</td>
                <td>{{ $detalle->producto->unidad }}</td>
                <td class="text-right"><strong>{{ number_format($detalle->cantidad, 2) }}</strong></td>
                <td class="text-right">${{ number_format($detalle->costo, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <div style="text-align: right; margin-top: 10px;">
        <table style="width: 250px; margin-left: auto;">
            <tr>
                <td style="padding: 3px;"><strong>Total Productos:</strong></td>
                <td style="padding: 3px; text-align: right;">{{ $traspaso->detalles->count() }}</td>
            </tr>
            <tr>
                <td style="padding: 3px;"><strong>Total Unidades:</strong></td>
                <td style="padding: 3px; text-align: right;">{{ number_format($traspaso->detalles->sum('cantidad'), 2) }}</td>
            </tr>
            <tr style="background-color: #667eea; color: white;">
                <td style="padding: 5px;"><strong>VALOR TOTAL:</strong></td>
                <td style="padding: 5px; text-align: right; font-size: 12px;">
                    <strong>${{ number_format($traspaso->detalles->sum(function($d) { return $d->cantidad * $d->costo; }), 2) }}</strong>
                </td>
            </tr>
        </table>
    </div>

    <!-- Observaciones -->
    @if($traspaso->observaciones)
    <div style="margin-top: 10px; border: 1px solid #ddd; padding: 8px; background-color: #f8f9fa;">
        <p style="font-weight: bold; margin-bottom: 5px;">Observaciones:</p>
        <p style="font-size: 10px;">{{ $traspaso->observaciones }}</p>
    </div>
    @endif

    <!-- Firmas -->
    <div class="signatures">
        <div class="sig-cell">
            <p><strong>ELABORÓ</strong></p>
            <div class="sig-line">
                Almacén Origen<br>
                {{ $traspaso->almacenOrigen->nombre }}
            </div>
        </div>
        <div class="sig-cell">
            <p><strong>TRANSPORTA</strong></p>
            <div class="sig-line">
                Nombre del Transportista<br>
                Fecha/Hora: __________
            </div>
        </div>
        <div class="sig-cell">
            <p><strong>RECIBE</strong></p>
            <div class="sig-line">
                Almacén Destino<br>
                {{ $traspaso->almacenDestino->nombre }}
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Este documento ampara el traslado de mercancía entre almacenes | No válido para efectos fiscales</p>
        <p>Generado: {{ now()->format('d/m/Y H:i:s') }} | Sistema ERP</p>
    </div>
</body>
</html>
