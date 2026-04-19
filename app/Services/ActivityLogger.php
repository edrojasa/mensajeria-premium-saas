<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

final class ActivityLogger
{
    public static function log(User $actor, string $action, ?string $description = null, ?Model $subject = null): void
    {
        $orgId = tenant_id();
        if ($orgId === null) {
            return;
        }

        ActivityLog::query()->create([
            'organization_id' => $orgId,
            'user_id' => $actor->id,
            'action' => $action,
            'description' => $description,
            'model_type' => $subject !== null ? $subject->getMorphClass() : null,
            'model_id' => $subject?->getKey(),
            'created_at' => now(),
        ]);
    }
}
