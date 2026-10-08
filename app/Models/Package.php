<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

class Package extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'guia_principal',
        'guia_secundaria',
        'guia_master',
        'total_paquetes',
        'estado',
        'client_id',
        'partner_id',
        'transportadora',
        'caja_numero',
        'total_cajas',
    ];

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
        static::created(fn (Package $package) => PackageLog::record($package->id, 'registrada', 'Guía registrada'));

        static::updated(function (Package $package) {
            $changes = Arr::except($package->getChanges(), ['updated_at', 'deleted_at']);

            if (array_key_exists('estado', $changes)) {
                $from = (string) $package->getOriginal('estado');
                $to = (string) $changes['estado'];

                PackageLog::record(
                    $package->id,
                    'estado_cambiado',
                    'Estado: ' . str_replace('_', ' ', $from) . ' → ' . str_replace('_', ' ', $to),
                    ['Estado' => [$from, $to]],
                );
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
}
