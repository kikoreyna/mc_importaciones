<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
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
