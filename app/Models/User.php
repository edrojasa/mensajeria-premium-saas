<?php

namespace App\Models;

use App\Actions\Auth\EnsureDefaultOrganizationMembership;
use App\Enums\UserAccountStatus;
use App\Organizations\OrganizationRole;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected static function booted(): void
    {
        static::created(function (User $user): void {
            DB::afterCommit(function () use ($user): void {
                $fresh = $user->fresh();

                if ($fresh === null) {
                    return;
                }

                app(EnsureDefaultOrganizationMembership::class)($fresh);
            });
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isAccountSuspended(): bool
    {
        return UserAccountStatus::isSuspended($this->status);
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withPivot(['role', 'is_active'])
            ->withTimestamps();
    }

    public function belongsToOrganization(int $organizationId): bool
    {
        return $this->organizations()->where('organizations.id', $organizationId)->exists();
    }

    public function organizationMembership(?int $organizationId = null): ?object
    {
        $organizationId ??= tenant_id();

        if ($organizationId === null) {
            return null;
        }

        return $this->organizations()->where('organizations.id', $organizationId)->first();
    }

    public function roleInCurrentOrganization(): ?string
    {
        return $this->organizationMembership()?->pivot->role;
    }

    public function isActiveInCurrentOrganization(): bool
    {
        $pivot = $this->organizationMembership()?->pivot;

        if ($pivot === null) {
            return false;
        }

        return (bool) ($pivot->is_active ?? true);
    }

    public function isMessenger(): bool
    {
        return $this->isActiveInCurrentOrganization()
            && $this->roleInCurrentOrganization() === OrganizationRole::MENSAJERO;
    }

    public function canOperateLogistics(): bool
    {
        if (! $this->isActiveInCurrentOrganization()) {
            return false;
        }

        return OrganizationRole::canOperateLogistics($this->roleInCurrentOrganization());
    }

    public function canManageCustomers(): bool
    {
        return $this->canOperateLogistics();
    }

    public function canViewOrganizationUsers(): bool
    {
        return $this->canManageOrganizationUsers();
    }

    public function canManageOrganizationUsers(): bool
    {
        if (! $this->isActiveInCurrentOrganization()) {
            return false;
        }

        return OrganizationRole::hasFullAccess($this->roleInCurrentOrganization());
    }

    /**
     * Exportaciones Excel/PDF (admin u operador; no mensajeros).
     */
    public function canExportTenantReports(): bool
    {
        return $this->canOperateLogistics();
    }

    /**
     * Finanzas: tarifas, cartera, reportes y pagos en envíos (admin u operador; no mensajeros).
     */
    public function canAccessFinancialModule(): bool
    {
        if (! $this->isActiveInCurrentOrganization()) {
            return false;
        }

        return in_array((string) $this->roleInCurrentOrganization(), [
            OrganizationRole::ADMIN,
            OrganizationRole::OPERADOR,
            'owner',
        ], true);
    }

    /**
     * Visor de auditoría / logs de actividad (solo administrador).
     */
    public function canViewAuditLogs(): bool
    {
        return $this->canManageOrganizationUsers();
    }

    public function assignedShipments(): HasMany
    {
        return $this->hasMany(Shipment::class, 'assigned_user_id');
    }
}
