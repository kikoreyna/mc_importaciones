<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Throwable;

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

    protected static function booted(): void
    {
        static::created(fn (PackagePhoto $photo) => PackageLog::record($photo->package_id, 'foto_agregada', 'Foto agregada'));
        static::deleted(fn (PackagePhoto $photo) => PackageLog::record($photo->package_id, 'foto_eliminada', 'Foto eliminada'));
    }

    public function getDisplayUrlAttribute(): string
    {
        if ($this->path && config('filesystems.disks.s3.bucket')) {
            try {
                return Storage::disk('s3')->temporaryUrl($this->path, now()->addMinutes(10));
            } catch (Throwable) {
            }
        }

        return (string) $this->url;
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
