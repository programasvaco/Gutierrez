<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Traspaso {{ $traspaso->folio }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            padding: 20px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #667eea;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #667eea;
            font-size: 24px;
            margin-bottom: 5px;
        }
        
        .header h2 {
            color: #764ba2;
            font-size: 18px;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 15px;
        }
        
        .info-col {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .info-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-right: 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
        }
        
        .info-box h3 {
            color: #667eea;
            font-size: 14px;
            margin-bottom: 8px;
            border-bottom: 1px solid #667eea;
            padding-bottom: 5px;
        }
        
        .info-box p {
            margin: 5px 0;
            line-height: 1.6;
        }
        
        .info-box strong {
            color: #555;
        }
        
        .status-badge {
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
        }
        
        .status-creado { background-color: #6c757d; color: white; }
        .status-en-transito { background-color: #ffc107; color: #333; }
        .status-recibido { background-color: #28a745; color: white; }
        .status-cancelado { background-color: #dc3545; color: white; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        table thead {
            background-color: #667eea;
            color: white;
        }
        
        table th {
            padding: 10px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }
        
        table td {
            padding: 8px 10px;
            border-bottom: 1px solid #ddd;
        }
        
        table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        
        .text-right {
            text-align: right;
        }
        
        .text-center {
            text-align: center;
        }
        
        .totals {
            margin-top: 20px;
            text-align: right;
        }
        
        .totals table {
            width: 300px;
            margin-left: auto;
        }
        
        .totals td {
            padding: 5px 10px;
        }
        
        .totals .total-row {
            font-weight: bold;
            font-size: 14px;
            background-color: #667eea;
            color: white;
        }
        
        .observations {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
            background-color: #fffbea;
            border-left: 4px solid #ffc107;
        }
        
        .observations h4 {
            color: #856404;
            margin-bottom: 8px;
        }
        
        .signatures {
            margin-top: 50px;
            display: table;
            width: 100%;
        }
        
        .signature-box {
            display: table-cell;
            width: 50%;
            text-align: center;
            padding: 20px;
        }
        
        .signature-line {
            border-top: 2px solid #333;
            margin-top: 60px;
            padding-top: 5px;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        @page {
            margin: 100px 50px;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>TRASPASO DE MERCANCÍA</h1>
        <h2>Sistema de Gestión de Inventario</h2>
    </div>

    <!-- Información General -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-col">
                <div class="info-box">
                    <h3>Datos del Traspaso</h3>
                    <p><strong>Folio:</strong> {{ $traspaso->folio }}</p>
                    <p><strong>Fecha:</strong> {{ $traspaso->fecha->format('d/m/Y') }}</p>
                    <p><strong>Hora:</strong> {{ date('H:i:s', strtotime($traspaso->hora)) }}</p>
                    <p><strong>Estado:</strong> 
                        <span class="status-badge status-{{ str_replace(' ', '-', $traspaso->status) }}">
                            {{ ucfirst($traspaso->status) }}
                        </span>
                    </p>
                </div>
            </div>
            <div class="info-col">
                <div class="info-box">
                    <h3>Timeline</h3>
                    <p><strong>Creado:</strong> {{ $traspaso->created_at->format('d/m/Y H:i') }}</p>
                    @if($traspaso->fecha_transito)
                    <p><strong>En Tránsito:</strong> {{ $traspaso->fecha_transito->format('d/m/Y H:i') }}</p>
                    @endif
                    @if($traspaso->fecha_recepcion)
                    <p><strong>Recibido:</strong> {{ $traspaso->fecha_recepcion->format('d/m/Y H:i') }}</p>
                    @endif
                    @if($traspaso->fecha_cancelacion)
                    <p><strong>Cancelado:</strong> {{ $traspaso->fecha_cancelacion->format('d/m/Y H:i') }}</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="info-row">
            <div class="info-col">
                <div class="info-box">
                    <h3>📤 Almacén Origen (Salida)</h3>
                    <p><strong>Nombre:</strong> {{ $traspaso->almacenOrigen->nombre }}</p>
                    <p><strong>Dirección:</strong> {{ $traspaso->almacenOrigen->domicilio }}</p>
                    <p><strong>Ciudad:</strong> {{ $traspaso->almacenOrigen->ciudad }}</p>
                </div>
            </div>
            <div class="info-col">
                <div class="info-box">
                    <h3>📥 Almacén Destino (Entrada)</h3>
                    <p><strong>Nombre:</strong> {{ $traspaso->almacenDestino->nombre }}</p>
                    <p><strong>Dirección:</strong> {{ $traspaso->almacenDestino->domicilio }}</p>
                    <p><strong>Ciudad:</strong> {{ $traspaso->almacenDestino->ciudad }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Productos -->
    <h3 style="color: #667eea; margin-bottom: 10px;">Productos Traspasados</h3>
    <table>
        <thead>
            <tr>
                <th style="width: 10%;">Código</th>
                <th style="width: 40%;">Descripción</th>
                <th style="width: 15%;">Unidad</th>
                <th style="width: 15%;" class="text-right">Cantidad</th>
                <th style="width: 20%;" class="text-right">Costo Unitario</th>
            </tr>
        </thead>
        <tbody>
            @foreach($traspaso->detalles as $detalle)
            <tr>
                <td><strong>{{ $detalle->producto->codigo }}</strong></td>
                <td>{{ $detalle->producto->descripcion }}</td>
                <td>{{ $detalle->producto->unidad }}</td>
                <td class="text-right">{{ number_format($detalle->cantidad, 2) }}</td>
                <td class="text-right">${{ number_format($detalle->costo, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totales -->
    <div class="totals">
        <table>
            <tr>
                <td><strong>Total de Productos:</strong></td>
                <td class="text-right">{{ $traspaso->detalles->count() }}</td>
            </tr>
            <tr>
                <td><strong>Total Unidades:</strong></td>
                <td class="text-right">{{ number_format($traspaso->detalles->sum('cantidad'), 2) }}</td>
            </tr>
            <tr class="total-row">
                <td><strong>Valor Total:</strong></td>
                <td class="text-right">
                    ${{ number_format($traspaso->detalles->sum(function($d) { return $d->cantidad * $d->costo; }), 2) }}
                </td>
            </tr>
        </table>
    </div>

    <!-- Observaciones -->
    @if($traspaso->observaciones)
    <div class="observations">
        <h4>📝 Observaciones:</h4>
        <p>{{ $traspaso->observaciones }}</p>
    </div>
    @endif

    <!-- Firmas -->
    <div class="signatures">
        <div class="signature-box">
            <p><strong>ENTREGA</strong></p>
            <p>{{ $traspaso->almacenOrigen->nombre }}</p>
            <div class="signature-line">
                Firma y Sello
            </div>
        </div>
        <div class="signature-box">
            <p><strong>RECIBE</strong></p>
            <p>{{ $traspaso->almacenDestino->nombre }}</p>
            <div class="signature-line">
                Firma y Sello
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Documento generado el {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Sistema de Gestión de Inventario - {{ config('app.name', 'ERP') }}</p>
    </div>
</body>
</html>
