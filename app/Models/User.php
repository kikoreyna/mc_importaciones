<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public function isPackageManager(): bool
    {
        return $this->hasRole(['administrador', 'supervisor']);
    }

    public function canAccessDashboard(): bool
    {
        return $this->hasRole(['administrador', 'supervisor', 'documentador']);
    }

    public function isAdministrator(): bool
    {
        return $this->hasRole('administrador');
    }

    public function hasRole(array|string $roles): bool
    {
        return in_array(strtolower(trim((string) $this->role)), (array) $roles, true);
    }

    public static function roleLabels(): array
    {
        return [
            'administrador' => 'Administrador',
            'supervisor' => 'Supervisor',
            'documentador' => 'Documentador',
            'bodega_usa' => 'Bodega USA',
            'bodega_mex' => 'Bodega MEX',
        ];
    }

    public function roleLabel(): string
    {
        $role = strtolower(trim((string) $this->role));

        return self::roleLabels()[$role] ?? $this->role;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
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
}
