<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $table = 'attendance';

    protected $fillable = [
        'enrollment_id',
        'institucion_id',
        'grado',
        'seccion',
        'fecha',
        'estado',
        'justificacion',
        'archivo_path',
        'archivo_formato',
        'es_carga',
        'registrado_por',
    ];
}
