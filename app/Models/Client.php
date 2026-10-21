<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Concerns\HasActivityLog;

class Client extends Model
{
    use HasActivityLog, SoftDeletes;

    protected $fillable = [
        'partner_id',
        'nombre',
        'alias',
    ];

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
