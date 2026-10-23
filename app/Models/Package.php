<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'guia_principal',
        'guia_secundaria',
        'guia_master',
        'total_paquetes',
        'client_id',
        'partner_id',
        'transportadora',
        'caja_numero',
        'total_cajas',
        'transportadora_id',
        'bodega_usa_user_id',
        'bodega_mex_user_id',
        'documentador_user_id',
        'registered_at',
        'received_at',
        'received_mex_at',
        'documented_at',
    ];

    protected function casts(): array
    {
        return [
            'registered_at' => 'datetime',
            'received_at' => 'datetime',
            'received_mex_at' => 'datetime',
            'documented_at' => 'datetime',
        ];
    }

    // Azul: solo USA; amarillo: pasó por bodega MEX o se documentó sin pasar por ella; verde: ambas cosas.
    public static function colorFor(mixed $documentedAt, mixed $receivedMexAt): string
    {
        return match (true) {
            $documentedAt !== null && $receivedMexAt !== null => 'verde',
            $documentedAt !== null || $receivedMexAt !== null => 'amarillo',
            default => 'azul',
        };
    }

    public function getColorAttribute(): string
    {
        return self::colorFor($this->documented_at, $this->received_mex_at);
    }

    public function scopePendingDocumentation($query)
    {
        return $query->whereNull('documented_at');
    }

    public function scopeDocumented($query)
    {
        return $query->whereNotNull('documented_at');
    }

    public function scopeReceivedInMexico($query)
    {
        return $query->whereNotNull('received_mex_at');
    }

    public function scopeWithColor($query, string $color)
    {
        return match ($color) {
            'verde' => $query->whereNotNull('documented_at')->whereNotNull('received_mex_at'),
            'amarillo' => $query->where(fn ($outer) => $outer
                ->where(fn ($q) => $q->whereNull('documented_at')->whereNotNull('received_mex_at'))
                ->orWhere(fn ($q) => $q->whereNotNull('documented_at')->whereNull('received_mex_at'))),
            'azul' => $query->whereNull('documented_at')->whereNull('received_mex_at'),
            default => $query,
        };
    }

    public function markReceivedInMexico(): void
    {
        $this->update(['received_mex_at' => now(), 'bodega_mex_user_id' => Auth::id()]);
    }

    public function markDocumented(array $attributes = []): void
    {
        $this->update($attributes + ['documented_at' => now(), 'documentador_user_id' => Auth::id()]);
    }

    public function markPendingDocumentation(): void
    {
        $this->update(['documented_at' => null, 'documentador_user_id' => null]);
    }

    protected const FIELD_LABELS = [
        'guia_principal' => 'Guía principal',
        'guia_secundaria' => 'Guía secundaria',
        'guia_master' => 'Guía máster',
        'total_paquetes' => 'Total de paquetes',
        'client_id' => 'Cliente',
        'partner_id' => 'Socio',
        'transportadora' => 'Transportadora',
        'caja_numero' => 'Caja #',
        'total_cajas' => 'Total de cajas',
    ];

    protected static function booted(): void
    {
        static::creating(function (Package $package) {
            $package->registered_at ??= now();
            $package->received_at ??= now();
            $package->bodega_usa_user_id ??= Auth::id();
        });

        static::created(fn (Package $package) => PackageLog::record($package->id, 'registrada', 'Guía registrada'));

        static::updated(function (Package $package) {
            $changes = Arr::except($package->getChanges(), ['updated_at', 'deleted_at']);

            if (array_key_exists('documented_at', $changes) || array_key_exists('received_mex_at', $changes)) {
                $from = self::colorFor($package->getOriginal('documented_at'), $package->getOriginal('received_mex_at'));
                $to = $package->color;

                if ($from !== $to) {
                    PackageLog::record($package->id, 'color_cambiado', "Color: {$from} → {$to}", ['Color' => [$from, $to]]);
                }
            }

            $labelled = [];
            foreach (Arr::only($changes, array_keys(self::FIELD_LABELS)) as $field => $new) {
                $labelled[self::FIELD_LABELS[$field]] = [
                    self::displayValue($field, $package->getOriginal($field)),
                    self::displayValue($field, $new),
                ];
            }

            if ($labelled) {
                PackageLog::record($package->id, 'actualizada', 'Datos actualizados: ' . implode(', ', array_keys($labelled)), $labelled);
            }
        });

        static::deleted(fn (Package $package) => PackageLog::record($package->id, 'eliminada', 'Guía eliminada'));
        static::restored(fn (Package $package) => PackageLog::record($package->id, 'restaurada', 'Guía restaurada'));
    }

    protected static function displayValue(string $field, mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return match ($field) {
            'client_id' => Client::withTrashed()->find($value)?->nombre ?? (string) $value,
            'partner_id' => Partner::withTrashed()->find($value)?->nombre ?? (string) $value,
            default => (string) $value,
        };
    }

    public function logs(): HasMany
    {
        return $this->hasMany(PackageLog::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(PackagePhoto::class);
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
         return $this->belongsTo(Client::class);
    }

    public function partner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function transportadoraRelation(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Transportadora::class, 'transportadora_id');
    }

    public function bodegaUsaUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'bodega_usa_user_id')->withTrashed();
    }

    public function bodegaMexUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'bodega_mex_user_id')->withTrashed();
    }

    public function documentadorUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'documentador_user_id')->withTrashed();
    }
}
