<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    public const ROL_ADMIN = 'admin';

    public const ROL_MECANICO = 'mecanico';

    public const ROLES = [
        self::ROL_ADMIN,
        self::ROL_MECANICO,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
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

    public function esAdmin(): bool
    {
        return $this->rol === self::ROL_ADMIN;
    }

    public function esMecanico(): bool
    {
        return $this->rol === self::ROL_MECANICO;
    }

    protected function rolLabel(): Attribute
    {
        return Attribute::get(
            fn (): string => $this->esAdmin() ? 'Administrador' : 'Mecánico'
        );
    }

    protected function rolBadge(): Attribute
    {
        return Attribute::get(
            fn (): string => $this->esAdmin() ? 'badge-soft-purple' : 'badge-soft-blue'
        );
    }

    protected function iniciales(): Attribute
    {
        return Attribute::get(function (): string {
            $partes = preg_split('/\s+/', trim((string) $this->name)) ?: [];

            $iniciales = collect($partes)
                ->filter()
                ->take(2)
                ->map(fn (string $parte): string => mb_substr($parte, 0, 1))
                ->implode('');

            return mb_strtoupper($iniciales !== '' ? $iniciales : 'U');
        });
    }
}
