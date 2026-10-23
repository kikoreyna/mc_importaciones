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
                // La expiración se redondea a ventanas de 6 h para que la URL se repita y el navegador pueda cachear la foto.
                $window = 6 * 3600;
                $expiresAt = now()->setTimestamp((intdiv(time(), $window) + 2) * $window);

                return Storage::disk('s3')->temporaryUrl($this->path, $expiresAt);
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
