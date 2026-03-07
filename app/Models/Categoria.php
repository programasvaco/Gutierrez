<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categoria extends Model
{
    use HasFactory;
    
    protected $table = 'categorias';

    protected $fillable = [
        'nombre',
    ];

    /**
     * Relación con productos
     */
    public function productos()
    {
        return $this->hasMany(Producto::class);
    }

    /**
     * Contar productos de la categoría
     */
    public function getTotalProductosAttribute()
    {
        return $this->productos()->count();
    }
}
