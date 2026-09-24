<?php

namespace App\Services;

use App\Models\Package;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Throwable;

class PackagePhotoUploadService
{
    public function handle(array $files, Package $package): void
    {
        if (empty($files)) {
            return;
        }

        $disk = $this->resolveDisk();

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile || ! $file->isValid()) {
                continue;
            }

            $folder = 'packages/' . $package->id;
            $filename = uniqid('', true) . '-' . $this->sanitizeFilename($file->getClientOriginalName());
            try {
                $path = $file->storeAs($folder, $filename, $disk);
            } catch (Throwable $exception) {
                Log::error('No se pudo guardar la foto del paquete.', [
                    'package_id' => $package->id,
                    'disk' => $disk,
                    'error' => $exception->getMessage(),
                ]);

                continue;
            }

            if (! is_string($path) || $path === '') {
                continue;
            }

            $package->photos()->create([
                'file_name' => $filename,
                'path' => $path,
                'url' => Storage::disk($disk)->url($path),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize(),
            ]);
        }
    }

    protected function resolveDisk(): string
    {
        if (empty(config('filesystems.disks.s3.key')) || empty(config('filesystems.disks.s3.secret')) || empty(config('filesystems.disks.s3.bucket'))) {
            return 'local';
        }

        return 's3';
    }

    protected function sanitizeFilename(string $name): string
    {
        $name = preg_replace('/[^A-Za-z0-9_.-]/', '-', $name);

        return trim((string) $name, '-');
    }
}
