<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'tipo',
        'nombres',
        'apellidos',
        'dni',
        'fecha_nacimiento',
        'direccion',
        'telefono',
        'email',
        'institucion_id',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'persona_id');
    }
}



