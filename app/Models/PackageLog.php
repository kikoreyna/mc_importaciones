<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'package_id',
        'user_id',
        'user_name',
        'user_role',
        'action',
        'description',
        'changes',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public static function record(int $packageId, string $action, string $description, ?array $changes = null): void
    {
        $user = auth()->user();

        static::create([
            'package_id' => $packageId,
            'user_id' => $user?->id,
            'user_name' => $user?->name,
            'user_role' => $user?->role,
            'action' => $action,
            'description' => $description,
            'changes' => $changes ?: null,
            'created_at' => now(),
        ]);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class)->withTrashed();
    }
}
