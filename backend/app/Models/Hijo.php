<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hijo extends Model
{
    use HasFactory;

    protected $table = 'hijos';

    protected $fillable = [
        'nombre',
        'grado',
        'grupo',
        'codigo_qr',
        'padre_id',
    ];

    public function padre()
    {
        return $this->belongsTo(Usuario::class, 'padre_id');
    }
}
