<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

final class ActivityLogger
{
    private static ?bool $hasMetaColumn = null;

    /**
     * @param  array<string, mixed>|null  $meta
     */
    public static function log(User $actor, string $action, ?string $description = null, ?Model $subject = null, ?array $meta = null): void
    {
        $orgId = tenant_id();
        if ($orgId === null) {
            return;
        }

        $payload = [
            'organization_id' => $orgId,
            'user_id' => $actor->id,
            'action' => $action,
            'description' => $description,
            'model_type' => $subject !== null ? $subject->getMorphClass() : null,
            'model_id' => $subject?->getKey(),
            'created_at' => now(),
        ];

        if (self::hasMetaColumn()) {
            $payload['meta'] = $meta;
        }

        ActivityLog::query()->create($payload);
    }

    private static function hasMetaColumn(): bool
    {
        if (self::$hasMetaColumn !== null) {
            return self::$hasMetaColumn;
        }

        self::$hasMetaColumn = Schema::hasColumn('activity_logs', 'meta');

        return self::$hasMetaColumn;
    }
}
