<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetalleTraspaso extends Model
{
    use HasFactory;

    protected $fillable = [
        'traspaso_id',
        'producto_id',
        'cantidad',
        'costo'
    ];

    protected $casts = [
        'cantidad' => 'decimal:2',
        'costo' => 'decimal:2',
    ];

    /**
     * Relación con Traspaso
     */
    public function traspaso()
    {
        return $this->belongsTo(Traspaso::class);
    }

    /**
     * Relación con Producto
     */
    public function producto()
    {
        return $this->belongsTo(Producto::class);
    }
}
