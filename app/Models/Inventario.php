<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inventario extends Model
{
    use HasFactory;

    protected $table = 'inventario';

    protected $fillable = [
        'almacen_id',
        'producto_id',
        'existencia'
    ];

    protected $casts = [
        'existencia' => 'decimal:2',
    ];

    /**
     * Relación con Almacen
     */
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    /**
     * Relación con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }

    /**
     * Incrementar existencia con registro en kardex
     */
    public static function incrementarExistencia(
        $almacen_id, 
        $producto_id, 
        $cantidad, 
        $costo = 0,
        $documento = 'Compra',
        $referencia_doc = '',
        $fecha = null
    ) {
        $inventario = self::firstOrCreate(
            [
                'almacen_id' => $almacen_id,
                'producto_id' => $producto_id
            ],
            ['existencia' => 0]
        );

        $inventario->existencia += $cantidad;
        $inventario->save();

        // Registrar en kardex
        Kardex::registrarEntrada(
            $producto_id,
            $almacen_id,
            $documento,
            $referencia_doc,
            $cantidad,
            $costo,
            $fecha
        );

        return $inventario;
    }

    /**
     * Decrementar existencia con registro en kardex
     */
    public static function decrementarExistencia(
        $almacen_id, 
        $producto_id, 
        $cantidad,
        $costo = 0,
        $documento = 'Cancelación de compra',
        $referencia_doc = '',
        $fecha = null
    ) {
        $inventario = self::where('almacen_id', $almacen_id)
            ->where('producto_id', $producto_id)
            ->first();

        if ($inventario) {
            $inventario->existencia -= $cantidad;
            $inventario->save();

            // Registrar en kardex
            Kardex::registrarSalida(
                $producto_id,
                $almacen_id,
                $documento,
                $referencia_doc,
                $cantidad,
                $costo,
                $fecha
            );
        }

        return $inventario;
    }
}
