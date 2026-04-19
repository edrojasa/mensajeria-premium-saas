<?php

namespace App\Http\Controllers;

use App\Support\TenantManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrganizationSwitcherController extends Controller
{
    public function update(Request $request, TenantManager $tenantManager): RedirectResponse
    {
        $validated = $request->validate([
            'organization_id' => ['required', 'integer', 'exists:organizations,id'],
        ]);

        $user = $request->user();

        $tenantManager->switchOrganization($user, (int) $validated['organization_id']);

        return redirect()->intended(route('dashboard'));
    }
}
