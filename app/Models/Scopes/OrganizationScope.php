<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Filtra consultas por organization_id cuando existe contexto de tenant en sesión.
 */
class OrganizationScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = tenant_id();

        if ($tenantId !== null) {
            $builder->where($model->getTable().'.organization_id', $tenantId);
        }
    }
}
