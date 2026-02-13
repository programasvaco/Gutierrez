<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Salida {{ $traspaso->folio }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #dc3545; padding-bottom: 15px; }
        .header h1 { color: #dc3545; font-size: 22px; }
        .header h2 { color: #666; font-size: 16px; font-weight: normal; }
        .info-box { border: 2px solid #dc3545; padding: 15px; margin-bottom: 20px; background-color: #fff5f5; }
        .info-box h3 { color: #dc3545; margin-bottom: 10px; }
        .info-box p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table thead { background-color: #dc3545; color: white; }
        table th, table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .alert { background-color: #fff3cd; border-left: 4px solid #ffc107; padding: 10px; margin: 20px 0; }
        .signature { margin-top: 80px; border-top: 2px solid #333; padding-top: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔻 ORDEN DE SALIDA DE ALMACÉN</h1>
        <h2>{{ $traspaso->almacenOrigen->nombre }}</h2>
    </div>

    <div class="info-box">
        <h3>Datos del Traspaso</h3>
        <p><strong>Folio:</strong> {{ $traspaso->folio }}</p>
        <p><strong>Fecha de Salida:</strong> {{ $traspaso->fecha->format('d/m/Y') }} - {{ date('H:i', strtotime($traspaso->hora)) }}</p>
        <p><strong>Destino:</strong> {{ $traspaso->almacenDestino->nombre }} - {{ $traspaso->almacenDestino->ciudad }}</p>
    </div>

    <div class="alert">
        <strong>⚠️ IMPORTANTE:</strong> Verificar que las cantidades sean correctas antes de autorizar la salida. 
        Este documento debe ser firmado por el responsable del almacén.
    </div>

    <h3 style="color: #dc3545; margin-bottom: 10px;">Productos a Entregar</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 45%;">Descripción</th>
                <th style="width: 15%;">Unidad</th>
                <th style="width: 15%;" class="text-right">Cantidad</th>
                <th style="width: 10%;">Verificado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($traspaso->detalles as $detalle)
            <tr>
                <td><strong>{{ $detalle->producto->codigo }}</strong></td>
                <td>{{ $detalle->producto->descripcion }}</td>
                <td>{{ $detalle->producto->unidad }}</td>
                <td class="text-right"><strong>{{ number_format($detalle->cantidad, 2) }}</strong></td>
                <td style="text-align: center;">☐</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 20px;"><strong>Total de productos:</strong> {{ $traspaso->detalles->count() }} | 
       <strong>Total de unidades:</strong> {{ number_format($traspaso->detalles->sum('cantidad'), 2) }}</p>

    @if($traspaso->observaciones)
    <div style="margin-top: 20px; border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;">
        <strong>Observaciones:</strong> {{ $traspaso->observaciones }}
    </div>
    @endif

    <div class="signature">
        <p><strong>AUTORIZA SALIDA</strong></p>
        <p>Responsable de Almacén</p>
        <p style="margin-top: 50px;">_________________________________</p>
        <p>Nombre y Firma</p>
        <p style="margin-top: 10px;">Fecha: ___________________</p>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
