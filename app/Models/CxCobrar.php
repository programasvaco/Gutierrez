<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CxCobrar extends Model
{
    protected $table = 'cxcobrar';

    protected $fillable = [
        'cliente_id',
        'venta_id',
        'fecha',
        'fecha_vencimiento',
        'importe',
        'saldo',
    ];

    protected $casts = [
        'fecha'             => 'date',
        'fecha_vencimiento' => 'date',
        'importe'           => 'decimal:2',
        'saldo'             => 'decimal:2',
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    public function getVencidaAttribute(): bool
    {
        return $this->fecha_vencimiento < now() && $this->saldo > 0;
    }

    public function getCobradaAttribute(): bool
    {
        return $this->saldo <= 0;
    }
}
