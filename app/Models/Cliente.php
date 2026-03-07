<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'nombre',
        'razon_social',
        'domicilio',
        'ciudad',
        'cpostal',
        'rfc',
        'telefono',
        'correoe',
        'status',
    ];

    public function scopeActivos($query)
    {
        return $query->where('status', 'activo');
    }

    public function scopeInactivos($query)
    {
        return $query->where('status', 'inactivo');
    }

    public function setRfcAttribute($value)
    {
        $this->attributes['rfc'] = $value ? strtoupper($value) : null;
    }

    public function setCorreoeAttribute($value)
    {
        $this->attributes['correoe'] = $value ? strtolower($value) : null;
    }
}
