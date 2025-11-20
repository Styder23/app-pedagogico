<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Institucion extends Model
{
    use HasFactory;

    protected $table = 'instituciones';

    protected $fillable = [
        'nombre',
        'codigo_ugel',
        'direccion',
    ];

    public function personas(): HasMany
    {
        return $this->hasMany(Persona::class, 'institucion_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'institucion_id');
    }
}



