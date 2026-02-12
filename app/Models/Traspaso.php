<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Traspaso extends Model
{
    use HasFactory;

    protected $fillable = [
        'folio',
        'fecha',
        'hora',
        'almacen_origen_id',
        'almacen_destino_id',
        'status',
        'fecha_transito',
        'fecha_recepcion',
        'fecha_cancelacion',
        'observaciones'
    ];

    protected $casts = [
        'fecha' => 'date',
        'fecha_transito' => 'datetime',
        'fecha_recepcion' => 'datetime',
        'fecha_cancelacion' => 'datetime',
    ];

    /**
     * Relación con Almacen Origen
     */
    public function almacenOrigen()
    {
        return $this->belongsTo(Almacen::class, 'almacen_origen_id');
    }

    /**
     * Relación con Almacen Destino
     */
    public function almacenDestino()
    {
        return $this->belongsTo(Almacen::class, 'almacen_destino_id');
    }

    /**
     * Relación con DetalleTraspaso
     */
    public function detalles()
    {
        return $this->hasMany(DetalleTraspaso::class);
    }

    /**
     * Verificar si puede ser cancelado
     */
    public function puedeCancelarse()
    {
        return $this->status === 'creado';
    }

    /**
     * Verificar si puede ponerse en tránsito
     */
    public function puedePonerseEnTransito()
    {
        return $this->status === 'creado';
    }

    /**
     * Verificar si puede ser recibido
     */
    public function puedeRecibirse()
    {
        return $this->status === 'en transito';
    }

    /**
     * Scope para traspasos pendientes de recibir
     */
    public function scopePendientesRecibir($query, $almacen_id = null)
    {
        $query->where('status', 'en transito');
        
        if ($almacen_id) {
            $query->where('almacen_destino_id', $almacen_id);
        }
        
        return $query;
    }

    /**
     * Scope para traspasos por almacén origen
     */
    public function scopePorAlmacenOrigen($query, $almacen_id)
    {
        return $query->where('almacen_origen_id', $almacen_id);
    }

    /**
     * Scope para traspasos por almacén destino
     */
    public function scopePorAlmacenDestino($query, $almacen_id)
    {
        return $query->where('almacen_destino_id', $almacen_id);
    }

    /**
     * Obtener badge de color según status
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'creado' => 'bg-secondary',
            'en transito' => 'bg-warning',
            'recibido' => 'bg-success',
            'cancelado' => 'bg-danger',
        ];

        return $badges[$this->status] ?? 'bg-secondary';
    }

    /**
     * Obtener icono según status
     */
    public function getStatusIconAttribute()
    {
        $icons = [
            'creado' => 'fa-file-alt',
            'en transito' => 'fa-truck',
            'recibido' => 'fa-check-circle',
            'cancelado' => 'fa-times-circle',
        ];

        return $icons[$this->status] ?? 'fa-file-alt';
    }
}
