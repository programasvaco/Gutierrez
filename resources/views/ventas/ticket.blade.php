<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket {{ $venta->folio }}</title>
    <style>
        @page {
            size: 80mm auto;
            margin: 3mm 3mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            width: 74mm;
            color: #000;
        }
        .center  { text-align: center; }
        .right   { text-align: right; }
        .bold    { font-weight: bold; }
        .large   { font-size: 14px; }
        .small   { font-size: 9px; }

        hr {
            border: none;
            border-top: 1px dashed #000;
            margin: 4px 0;
        }

        .empresa-nombre {
            font-size: 15px;
            font-weight: bold;
            text-align: center;
            line-height: 1.2;
        }
        .empresa-info {
            text-align: center;
            font-size: 9px;
            line-height: 1.4;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }
        table.items th {
            font-size: 9px;
            border-bottom: 1px dashed #000;
            padding-bottom: 2px;
            margin-bottom: 2px;
        }
        table.items td {
            font-size: 10px;
            vertical-align: top;
            padding: 1px 0;
        }
        table.items td.desc {
            width: 46mm;
            word-break: break-word;
        }
        table.items td.cant {
            width: 10mm;
            text-align: right;
        }
        table.items td.precio {
            width: 18mm;
            text-align: right;
        }

        table.totales td {
            padding: 1px 0;
            font-size: 11px;
        }
        table.totales td.label { width: 50%; }
        table.totales td.valor { width: 50%; text-align: right; }

        .total-final {
            font-size: 15px;
            font-weight: bold;
            text-align: right;
            border-top: 1px dashed #000;
            padding-top: 3px;
        }

        .gracias {
            text-align: center;
            font-size: 10px;
            margin-top: 6px;
            line-height: 1.5;
        }

        @media print {
            body { width: 74mm; }
        }
    </style>
</head>
<body>

    {{-- Encabezado empresa --}}
    <div class="empresa-nombre">DISTRIBUIDORA GUTIERREZ</div>
    <div class="empresa-info">
        {{ $venta->almacen->ciudad ?? '' }}<br>
        Tel: {{ $venta->almacen->telefono ?? '' }}
    </div>

    <hr>

    {{-- Datos del ticket --}}
    <table>
        <tr>
            <td class="bold">Folio:</td>
            <td class="right">{{ $venta->folio }}</td>
        </tr>
        <tr>
            <td class="bold">Fecha:</td>
            <td class="right">{{ $venta->fecha->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td class="bold">Pago:</td>
            <td class="right">{{ ucfirst($venta->tipo_pago) }}</td>
        </tr>
        @if($venta->cliente)
        <tr>
            <td class="bold">Cliente:</td>
            <td class="right">{{ $venta->cliente->nombre }}</td>
        </tr>
        @endif
    </table>

    <hr>

    {{-- Artículos --}}
    <table class="items">
        <thead>
            <tr>
                <th class="left">Descripción</th>
                <th class="right" style="text-align:right">Cant</th>
                <th class="right" style="text-align:right">Importe</th>
            </tr>
        </thead>
        <tbody>
            @foreach($venta->detalles as $det)
            <tr>
                <td class="desc">
                    {{ Str::limit($det->producto->descripcion, 28) }}<br>
                    <span class="small">@ ${{ number_format($det->precio, 2) }}</span>
                </td>
                <td class="cant">{{ number_format($det->cantidad, 2) }}</td>
                <td class="precio">${{ number_format($det->subtotal, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <hr>

    {{-- Totales --}}
    <div class="total-final">
        TOTAL &nbsp; ${{ number_format($venta->total, 2) }}
    </div>

    <hr>

    {{-- Pie --}}
    <div class="gracias">
        ¡Gracias por su compra!<br>
        <span class="small">{{ now()->format('d/m/Y H:i') }}</span>
    </div>

</body>
<script>
    window.addEventListener('load', function () {
        window.print();
    });
</script>
</html>
