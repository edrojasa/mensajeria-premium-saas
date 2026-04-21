<?php

namespace App\Http\Controllers;

use App\Audit\AuditActions;
use App\Http\Requests\StoreOrganizationUserRequest;
use App\Models\User;
use App\Organizations\OrganizationRole;
use App\Services\ActivityLogger;
use App\Support\OrganizationUsersListing;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class OrganizationUserController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(auth()->user()->canViewOrganizationUsers(), 403);

        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        $users = OrganizationUsersListing::filteredQuery($request, $orgId)->get();

        return view('users.index', [
            'orgUsers' => $users,
            'canManage' => auth()->user()->canManageOrganizationUsers(),
            'canAdmin' => OrganizationRole::hasFullAccess(auth()->user()->roleInCurrentOrganization()),
            'roleFilter' => $request->query('role'),
        ]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->canManageOrganizationUsers(), 403);

        return view('users.create');
    }

    public function store(StoreOrganizationUserRequest $request): RedirectResponse
    {
        $orgId = tenant_id();
        if ($orgId === null) {
            abort(403);
        }

        $validated = $request->validated();

        /** @var User|null $created */
        $created = null;

        DB::transaction(function () use ($validated, $orgId, &$created): void {
            $created = User::query()->create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'] ?? null,
                'password' => Hash::make($validated['password']),
            ]);

            $created->organizations()->attach($orgId, [
                'role' => $validated['role'],
                'is_active' => $validated['is_active'],
            ]);
        });

        if ($created !== null) {
            ActivityLogger::log(
                $request->user(),
                AuditActions::USER_CREATED,
                __('audit.user_created', ['name' => $created->name, 'email' => $created->email]),
                $created
            );
        }

        return redirect()
            ->route('users.index')
            ->with('status', __('users.created_success'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        abort_unless(auth()->user()->canManageOrganizationUsers(), 403);

        $orgId = tenant_id();
        if ($orgId === null || ! $user->belongsToOrganization($orgId)) {
            abort(404);
        }

        $validated = $request->validate([
            'role' => ['required', Rule::in(OrganizationRole::ALL)],
            'is_active' => ['required', 'boolean'],
            'phone' => ['nullable', 'string', 'max:32'],
        ]);

        DB::transaction(function () use ($user, $validated, $orgId): void {
            $user->organizations()->updateExistingPivot($orgId, [
                'role' => $validated['role'],
                'is_active' => $validated['is_active'],
            ]);

            $user->forceFill([
                'phone' => $validated['phone'] ?? null,
            ])->save();
        });

        ActivityLogger::log(
            $request->user(),
            AuditActions::USER_UPDATED,
            __('audit.user_updated', ['name' => $user->name]),
            $user
        );

        return back()->with('status', __('users.updated_success'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        abort_unless(
            OrganizationRole::hasFullAccess(auth()->user()->roleInCurrentOrganization()),
            403
        );

        $orgId = tenant_id();
        if ($orgId === null || ! $user->belongsToOrganization($orgId)) {
            abort(404);
        }

        if ($request->user()->id === $user->id) {
            return back()->withErrors(__('users.cannot_delete_self'));
        }

        DB::transaction(function () use ($request, $user, $orgId): void {
            $user->organizations()->detach($orgId);
            $user->unsetRelation('organizations');

            ActivityLogger::log(
                $request->user(),
                AuditActions::USER_FORCE_DELETED,
                __('audit.user_removed_from_org', ['name' => $user->name]),
                null,
                [
                    'removed_user_id' => $user->id,
                    'organization_id' => $orgId,
                    'action' => 'detach_or_delete',
                ]
            );

            if (! $user->organizations()->exists()) {
                $user->delete();
            }
        });

        return redirect()
            ->route('users.index')
            ->with('status', __('users.removed_success'));
    }
}
