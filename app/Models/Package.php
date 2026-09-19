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
    ];

    public function photos(): HasMany
    {
        return $this->hasMany(PackagePhoto::class);
    }
}
