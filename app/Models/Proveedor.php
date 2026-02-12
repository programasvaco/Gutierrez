<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proveedor extends Model
{
    use HasFactory;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'razon_social',
        'rfc',
        'direccion',
        'ciudad',
        'telefono',
        'correo',
        'dias_plazo',
        'status'
    ];

    protected $casts = [
        'dias_plazo' => 'integer',
    ];

    /**
     * Scope para filtrar proveedores activos
     */
    public function scopeActivos($query)
    {
        return $query->where('status', 'activo');
    }

    /**
     * Scope para filtrar proveedores inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('status', 'inactivo');
    }

    /**
     * Formatea el RFC en mayúsculas
     */
    public function setRfcAttribute($value)
    {
        $this->attributes['rfc'] = strtoupper($value);
    }

    /**
     * Formatea el correo en minúsculas
     */
    public function setCorreoAttribute($value)
    {
        $this->attributes['correo'] = strtolower($value);
    }
}
