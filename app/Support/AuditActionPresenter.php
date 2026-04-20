<?php

namespace App\Support;

use Illuminate\Support\Facades\Lang;

final class AuditActionPresenter
{
    public static function label(string $action): string
    {
        /** @var array<string, string>|mixed $labels */
        $labels = Lang::get('audit.action_labels', []);

        if (is_array($labels) && isset($labels[$action])) {
            return (string) $labels[$action];
        }

        return $action;
    }
}
