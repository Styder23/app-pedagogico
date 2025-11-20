<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'persona_id',
        'section_id',
        'institucion_id',
        'grado',
        'seccion',
        'fecha_inscripcion',
        'estado',
        'observaciones',
        'archivo_path',
        'archivo_formato',
        'es_carga',
        'usuario_id',
    ];
}
