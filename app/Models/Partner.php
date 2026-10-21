<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasActivityLog;

class Partner extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'nombre',
        'alias',
    ];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
