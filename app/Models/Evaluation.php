<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'descripcion',
        'peso',
        'orden',
        'activo',
        'institucion_id',
        'grado',
        'seccion',
        'archivo_path',
        'archivo_formato',
        'es_carga',
        'usuario_id',
    ];
}
