<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Chofer extends Model
{
    use HasFactory;

    protected $table = 'choferes';

    protected $fillable = [
        'usuario_id',
        'licencia',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function rutas()
    {
        return $this->hasMany(Ruta::class, 'chofer_id');
    }
}
