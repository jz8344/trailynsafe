<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chofer extends Model
{
    use HasFactory;

    protected $table = 'choferes';

    protected $fillable = [
        'nombre',
        'apellidos',
        'numero_licencia',
        'curp',
        'telefono',
        'correo',
        'estado',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Eliminamos la relación con usuario ya que ahora es independiente
    // public function usuario()
    // {
    //     return $this->belongsTo(Usuario::class, 'usuario_id');
    // }

    public function rutas()
    {
        return $this->hasMany(Ruta::class, 'chofer_id');
    }
}
