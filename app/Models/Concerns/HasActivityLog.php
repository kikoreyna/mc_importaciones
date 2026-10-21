<?php

namespace App\Models\Concerns;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

trait HasActivityLog
{
    protected static function bootHasActivityLog(): void
    {
        static::created(function (Model $model) {
            ActivityLog::record($model, 'created', static::activityChanges($model, $model->getAttributes(), true));
        });

        static::updated(function (Model $model) {
            $changes = Arr::except($model->getChanges(), ['updated_at', 'deleted_at']);

            if ($changes !== []) {
                ActivityLog::record($model, 'updated', static::activityChanges($model, $changes));
            }
        });

        static::deleted(function (Model $model) {
            ActivityLog::record($model, 'deleted');
        });

        static::restored(function (Model $model) {
            ActivityLog::record($model, 'restored');
        });
    }

    protected static function activityChanges(Model $model, array $changes, bool $created = false): array
    {
        $logged = [];

        foreach ($changes as $field => $newValue) {
            if (in_array($field, ['id', 'created_at', 'updated_at', 'deleted_at', 'remember_token', 'email_verified_at'], true)) {
                continue;
            }

            if ($field === 'password') {
                $logged[$field] = ['[REDACTED]', '[REDACTED]'];

                continue;
            }

            $oldValue = $model->getRawOriginal($field);
            $logged[$field] = ! $created
                ? [$oldValue, $newValue]
                : [null, $newValue];
        }

        return $logged;
    }
}
