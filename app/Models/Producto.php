<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Producto extends Model
{
    use HasFactory;

    protected $table = 'productos';

    protected $fillable = [
        'codigo',
        'codigo_empaque',
        'descripcion',
        'unidad',
        'unidad_compra',
        'contenido',
        'stock_min',
        'stock_max',
        'status',
        'imagen'
    ];

    protected $casts = [
        'contenido' => 'decimal:2',
        'stock_min' => 'integer',
        'stock_max' => 'integer',
    ];

    /**
     * Scope para filtrar productos activos
     */
    public function scopeActivos($query)
    {
        return $query->where('status', 'activo');
    }

    /**
     * Scope para filtrar productos inactivos
     */
    public function scopeInactivos($query)
    {
        return $query->where('status', 'inactivo');
    }

    /**
     * Obtener la URL completa de la imagen
     */
    public function getImagenUrlAttribute()
    {
        if ($this->imagen) {
            return asset('storage/productos/' . $this->imagen);
        }
        return asset('images/no-image.png');
    }
}
