<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unidad extends Model
{
    use HasFactory;

    protected $table = 'unidades';

    protected $fillable = [
        'matricula',
        'modelo',
        'capacidad',
    ];

    public function rutas()
    {
        return $this->hasMany(Ruta::class, 'unidad_id');
    }
}
