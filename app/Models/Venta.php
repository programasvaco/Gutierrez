<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $fillable = [
        'folio',
        'fecha',
        'almacen_id',
        'cliente_id',
        'subtotal',
        'total',
    ];

    protected $casts = [
        'fecha'    => 'date',
        'subtotal' => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    public function almacen()
    {
        return $this->belongsTo(Almacen::class);
    }

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    public function detalles()
    {
        return $this->hasMany(DetalleVenta::class);
    }

    public static function generarFolio(): string
    {
        $ultimo = self::max('id') ?? 0;
        return 'VTA-' . str_pad($ultimo + 1, 6, '0', STR_PAD_LEFT);
    }
}
