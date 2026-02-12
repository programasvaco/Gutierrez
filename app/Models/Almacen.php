<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Almacen extends Model
{
    use HasFactory;

    protected $table = 'almacenes';

    protected $fillable = [
        'nombre',
        'domicilio',
        'ciudad',
        'status'
    ];

    /**
     * Scope para filtrar almacenes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('status', 'activo');
    }

    /**
     * Scope para filtrar almacenes inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('status', 'inactivo');
    }
}
