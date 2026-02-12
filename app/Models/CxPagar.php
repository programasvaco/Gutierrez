<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CxPagar extends Model
{
    use HasFactory;

    protected $table = 'cxpagar';

    protected $fillable = [
        'proveedor_id',
        'compra_id',
        'fecha',
        'fecha_vencimiento',
        'importe',
        'saldo'
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_vencimiento' => 'date',
        'importe' => 'decimal:2',
        'saldo' => 'decimal:2',
    ];

    /**
     * Relación con Proveedor
     */
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    /**
     * Relación con Compra
     */
    public function compra()
    {
        return $this->belongsTo(Compra::class);
    }

    /**
     * Verificar si está vencida
     */
    public function getVencidaAttribute()
    {
        return $this->fecha_vencimiento < now() && $this->saldo > 0;
    }

    /**
     * Verificar si está pagada
     */
    public function getPagadaAttribute()
    {
        return $this->saldo <= 0;
    }
}
