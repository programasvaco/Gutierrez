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
        'precio_venta',
        'precio_minimo',
        'status',
        'imagen'
    ];

    protected $casts = [
        'contenido' => 'decimal:2',
        'stock_min' => 'integer',
        'stock_max' => 'integer',
        'precio_venta' => 'decimal:2',
        'precio_minimo' => 'decimal:2',
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

        /**
     * Verificar si el precio de venta es menor al precio mínimo
     */
    public function getPrecioVentaEsMenorAttribute()
    {
        return $this->precio_venta < $this->precio_minimo;
    }

    /**
     * Calcular el margen de ganancia
     */
    public function getMargenGananciaAttribute()
    {
        if ($this->precio_minimo > 0) {
            return (($this->precio_venta - $this->precio_minimo) / $this->precio_minimo) * 100;
        }
        return 0;
    }

    /**
     * Formatear precio de venta como moneda
     */
    public function getPrecioVentaFormateadoAttribute()
    {
        return '$ ' . number_format($this->precio_venta, 2, '.', ',');
    }

    /**
     * Formatear precio mínimo como moneda
     */
    public function getPrecioMinimoFormateadoAttribute()
    {
        return '$ ' . number_format($this->precio_minimo, 2, '.', ',');
    }
}
