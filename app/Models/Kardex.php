<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Kardex extends Model
{
    use HasFactory;

    protected $table = 'kardex';

    protected $fillable = [
        'producto_id',
        'almacen_id',
        'documento',
        'referencia_doc',
        'fecha',
        'hora',
        'tipo_movimiento',
        'cantidad',
        'costo',
        'existencia_anterior',
        'existencia_nueva'
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'decimal:2',
        'costo' => 'decimal:2',
        'existencia_anterior' => 'decimal:2',
        'existencia_nueva' => 'decimal:2',
    ];

    /**
     * Relación con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Relación con Almacen
     */
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    /**
     * Registrar movimiento de entrada
     */
    public static function registrarEntrada(
        $producto_id,
        $almacen_id,
        $documento,
        $referencia_doc,
        $cantidad,
        $costo,
        $fecha = null
    ) {
        // Obtener existencia actual
        $inventario = Inventario::where('almacen_id', $almacen_id)
            ->where('producto_id', $producto_id)
            ->first();

        $existencia_anterior = $inventario ? $inventario->existencia : 0;
        $existencia_nueva = $existencia_anterior + $cantidad;

        $fecha = $fecha ?? now()->toDateString();
        $hora = now()->toTimeString();

        return self::create([
            'producto_id' => $producto_id,
            'almacen_id' => $almacen_id,
            'documento' => $documento,
            'referencia_doc' => $referencia_doc,
            'fecha' => $fecha,
            'hora' => $hora,
            'tipo_movimiento' => 'Entrada',
            'cantidad' => $cantidad,
            'costo' => $costo,
            'existencia_anterior' => $existencia_anterior,
            'existencia_nueva' => $existencia_nueva,
        ]);
    }

    /**
     * Registrar movimiento de salida
     */
    public static function registrarSalida(
        $producto_id,
        $almacen_id,
        $documento,
        $referencia_doc,
        $cantidad,
        $costo,
        $fecha = null
    ) {
        // Obtener existencia actual
        $inventario = Inventario::where('almacen_id', $almacen_id)
            ->where('producto_id', $producto_id)
            ->first();

        $existencia_anterior = $inventario ? $inventario->existencia : 0;
        $existencia_nueva = $existencia_anterior - $cantidad;

        $fecha = $fecha ?? now()->toDateString();
        $hora = now()->toTimeString();

        return self::create([
            'producto_id' => $producto_id,
            'almacen_id' => $almacen_id,
            'documento' => $documento,
            'referencia_doc' => $referencia_doc,
            'fecha' => $fecha,
            'hora' => $hora,
            'tipo_movimiento' => 'Salida',
            'cantidad' => $cantidad,
            'costo' => $costo,
            'existencia_anterior' => $existencia_anterior,
            'existencia_nueva' => $existencia_nueva,
        ]);
    }

    /**
     * Obtener kardex de un producto en un almacén
     */
    public static function obtenerKardexProducto($producto_id, $almacen_id, $fecha_desde = null, $fecha_hasta = null)
    {
        $query = self::where('producto_id', $producto_id)
            ->where('almacen_id', $almacen_id);

        if ($fecha_desde) {
            $query->where('fecha', '>=', $fecha_desde);
        }

        if ($fecha_hasta) {
            $query->where('fecha', '<=', $fecha_hasta);
        }

        return $query->orderBy('fecha', 'asc')
            ->orderBy('hora', 'asc')
            ->get();
    }
}
