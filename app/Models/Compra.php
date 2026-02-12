<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compra extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha',
        'referencia',
        'proveedor_id',
        'almacen_id',
        'subtotal',
        'impuestos',
        'total'
    ];

    protected $casts = [
        'fecha' => 'date',
        'subtotal' => 'decimal:2',
        'impuestos' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Relación con Proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación con Almacen
     */
    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    /**
     * Relación con DetalleCompra
     */
    public function detalles()
    {
        return $this->hasMany(DetalleCompra::class);
    }

    /**
     * Relación con CuentaPorPagar
     */
    public function cuentaPorPagar()
    {
        return $this->hasOne(CxPagar::class);
    }
}
