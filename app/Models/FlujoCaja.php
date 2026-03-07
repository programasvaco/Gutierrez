<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FlujoCaja extends Model
{
    protected $table = 'flujo_caja';

    protected $fillable = [
        'fecha',
        'tipo',
        'referencia',
        'cantidad',
        'almacen_id',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'cantidad' => 'decimal:2',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }
}
