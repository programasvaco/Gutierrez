<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleCompra extends Model
{
    use HasFactory;

    protected $fillable = [
        'compra_id',
        'producto_id',
        'cantidad',
        'costo',
        'impuestos',
        'subtotal'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /**
     * Relación con Compra
     */
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Relación con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
