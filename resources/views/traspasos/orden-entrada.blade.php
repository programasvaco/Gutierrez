<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Entrada {{ $traspaso->folio }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; font-size: 12px; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 3px solid #28a745; padding-bottom: 15px; }
        .header h1 { color: #28a745; font-size: 22px; }
        .header h2 { color: #666; font-size: 16px; font-weight: normal; }
        .info-box { border: 2px solid #28a745; padding: 15px; margin-bottom: 20px; background-color: #f0fff4; }
        .info-box h3 { color: #28a745; margin-bottom: 10px; }
        .info-box p { margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        table thead { background-color: #28a745; color: white; }
        table th, table td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        table tbody tr:nth-child(even) { background-color: #f8f9fa; }
        .text-right { text-align: right; }
        .alert { background-color: #d1ecf1; border-left: 4px solid #0c5460; padding: 10px; margin: 20px 0; color: #0c5460; }
        .signature { margin-top: 80px; border-top: 2px solid #333; padding-top: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔼 ORDEN DE ENTRADA A ALMACÉN</h1>
        <h2>{{ $traspaso->almacenDestino->nombre }}</h2>
    </div>

    <div class="info-box">
        <h3>Datos del Traspaso</h3>
        <p><strong>Folio:</strong> {{ $traspaso->folio }}</p>
        <p><strong>Fecha de Recepción:</strong> 
            @if($traspaso->fecha_recepcion)
                {{ $traspaso->fecha_recepcion->format('d/m/Y H:i') }}
            @else
                Pendiente
            @endif
        </p>
        <p><strong>Procedencia:</strong> {{ $traspaso->almacenOrigen->nombre }} - {{ $traspaso->almacenOrigen->ciudad }}</p>
        @if($traspaso->fecha_transito)
        <p><strong>Enviado el:</strong> {{ $traspaso->fecha_transito->format('d/m/Y H:i') }}</p>
        @endif
    </div>

    <div class="alert">
        <strong>📋 INSTRUCCIONES:</strong> Verificar físicamente que las cantidades recibidas coincidan con este documento. 
        En caso de discrepancia, reportar inmediatamente al supervisor antes de firmar.
    </div>

    <h3 style="color: #28a745; margin-bottom: 10px;">Productos a Recibir</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 15%;">Código</th>
                <th style="width: 35%;">Descripción</th>
                <th style="width: 12%;">Unidad</th>
                <th style="width: 13%;" class="text-right">Cant. Enviada</th>
                <th style="width: 13%;" class="text-right">Cant. Recibida</th>
                <th style="width: 12%;">Estado</th>
            </tr>
        </thead>
        <tbody>
            @foreach($traspaso->detalles as $detalle)
            <tr>
                <td><strong>{{ $detalle->producto->codigo }}</strong></td>
                <td>{{ $detalle->producto->descripcion }}</td>
                <td>{{ $detalle->producto->unidad }}</td>
                <td class="text-right"><strong>{{ number_format($detalle->cantidad, 2) }}</strong></td>
                <td style="background-color: #fffacd; text-align: right;">___________</td>
                <td style="text-align: center;">☐ OK  ☐ Daño</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px; border: 1px solid #28a745; padding: 15px; background-color: #f0fff4;">
        <p><strong>Resumen:</strong></p>
        <p>Total de productos diferentes: <strong>{{ $traspaso->detalles->count() }}</strong></p>
        <p>Total de unidades enviadas: <strong>{{ number_format($traspaso->detalles->sum('cantidad'), 2) }}</strong></p>
        <p style="margin-top: 10px;">Total de unidades recibidas: <strong>___________</strong></p>
    </div>

    @if($traspaso->observaciones)
    <div style="margin-top: 20px; border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;">
        <strong>Observaciones del remitente:</strong> {{ $traspaso->observaciones }}
    </div>
    @endif

    <div style="margin-top: 20px; border: 1px solid #ddd; padding: 10px;">
        <p><strong>Observaciones del receptor:</strong></p>
        <p style="margin-top: 10px;">_________________________________________________________________</p>
        <p style="margin-top: 5px;">_________________________________________________________________</p>
        <p style="margin-top: 5px;">_________________________________________________________________</p>
    </div>

    <div class="signature">
        <p><strong>RECIBE CONFORME</strong></p>
        <p>Responsable de Almacén</p>
        <p style="margin-top: 50px;">_________________________________</p>
        <p>Nombre y Firma</p>
        <p style="margin-top: 10px;">Fecha y Hora de Recepción: ___________________</p>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
