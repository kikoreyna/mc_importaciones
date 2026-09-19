<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackagePhoto extends Model
{
    protected $fillable = [
        'package_id',
        'file_name',
        'path',
        'url',
        'mime_type',
        'size',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
