<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable; // Si los empleados inician sesión
use Illuminate\Notifications\Notifiable;

class Empleados extends Authenticatable
{
    use Notifiable;

    protected $table = 'empleados';

    protected $fillable = [
        'id_detalle_compras',
        'id_perfil',
        'id_permiso',
        'name',
        'email',
        'password',
        'ventas',
        'email_verified_at',
    ];

    protected $casts = [
        'ventas' => 'array',
        'email_verified_at' => 'datetime',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relaciones potenciales (ajústalas según tus modelos)
    public function detalleCompra()
    {
        return $this->belongsTo(DetalleCompra::class, 'id_detalle_compras');
    }

    public function perfil()
    {
        return $this->belongsTo(Perfil::class, 'id_perfil');
    }

    public function permiso()
    {
        return $this->belongsTo(Permiso::class, 'id_permiso');
    }
}
