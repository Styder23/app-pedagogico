<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'persona_id',
        'institucion_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function institucion(): BelongsTo
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }

    public function roleKey(): string
    {
        // Si la relación no está cargada, intentar cargarla
        if (!$this->relationLoaded('role') && $this->role_id) {
            $this->load('role');
        }
        
        $value = $this->role->slug ?? $this->role->name ?? '';
        return Str::slug(Str::lower($value));
    }

    public function isAdmin(): bool
    {
        // Verificar por role_id = 1 (administrador)
        if ($this->role_id == 1) {
            return true;
        }
        // También verificar por nombre del rol por compatibilidad
        return in_array($this->roleKey(), ['admin', 'ugel', 'administrator', 'administrador']);
    }

    public function isDirector(): bool
    {
        return in_array($this->roleKey(), ['director', 'directora']);
    }

    public function isDocente(): bool
    {
        return in_array($this->roleKey(), ['docente']);
    }

    public function canManageUsers(): bool
    {
        return $this->isAdmin() || $this->isDirector();
    }
}
