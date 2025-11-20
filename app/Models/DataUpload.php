<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DataUpload extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipo',
        'institucion_id',
        'grado',
        'seccion',
        'evaluacion_tipo',
        'archivo',
        'formato',
        'usuario_id',
    ];

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
